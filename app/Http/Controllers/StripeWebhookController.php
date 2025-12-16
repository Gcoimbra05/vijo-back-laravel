<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\User;
use App\Services\CreditService;
use App\Services\MembershipManagementService;
use Illuminate\Support\Facades\Auth;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\BillingPortal\Session;

use Exception;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $stripeSecret = config('services.stripe.secret');
        $webhookSecret = config('services.stripe.webhook_secret');

        Stripe::setApiKey($stripeSecret);

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
            Log::info('Stripe Webhook Event Type: ' . $event->type);
            Log::info('Stripe Webhook Received: ' . $request->getContent());
            
            switch ($event->type) {
                case 'customer.subscription.created':
                    $subscription = $event->data->object;
                    $userId = $this->getUserIdFromSubscription($subscription);
                    $planId = $this->getPlanIdFromSubscription($subscription);

                    Subscription::create([
                        'user_id'                => $userId,
                        'pending_plan_id'        => $planId,
                        'stripe_customer_id'     => $subscription->customer,
                        'stripe_subscription_id' => $subscription->id,
                        'status'                 => 2, // 2: incomplete/pending until invoice.paid
                        'start_date'             => $subscription->start_date ? date('Y-m-d H:i:s', $subscription->start_date) : null,
                        'end_date'               => $subscription->current_period_end ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                        'cancel_at'              => $subscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                        'cancelled_at'           => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null,
                        'reason'                 => $subscription->cancellation_details->reason ?? null,
                        'cancel_at_period_end'   => $subscription->cancel_at_period_end ? 1 : 0,
                    ]);
                    Log::info('Subscription Created (pending payment): ' . $subscription->id);
                    break;

                case 'customer.subscription.updated':
                    $subscription = $event->data->object;
                    $previousAttributes = $event->data->previous_attributes ?? null;
                    $planId = $this->getPlanIdFromSubscription($subscription);
                    
                    // Check if this update requires payment confirmation
                    $requiresPaymentConfirmation = $this->subscriptionUpdateRequiresPayment(
                        $subscription, 
                        $previousAttributes
                    );

                    $statusMap = [
                        'active' => 1,
                        'incomplete' => 2,
                        'canceled' => 3,
                        'past_due' => 4,
                        'unpaid' => 5,
                    ];
                    $status = $statusMap[$subscription->status] ?? 2;

                    // If payment is required, mark as pending upgrade
                    if ($requiresPaymentConfirmation) {
                        Log::info('Update requires payment confirmation - marking as pending');
                        
                        // Store the pending plan change but don't provision yet
                        Subscription::where('stripe_subscription_id', $subscription->id)
                            ->update([
                                'pending_plan_id' => $planId, // Store intended plan
                                'status'          => $status,
                                'start_date'      => $subscription->start_date ? date('Y-m-d H:i:s', $subscription->start_date) : null,
                                'end_date'        => $subscription->current_period_end ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                                'cancel_at'       => $subscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                                'cancelled_at'    => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null,
                                'reason'          => $subscription->cancellation_details->reason ?? null,
                                'cancel_at_period_end' => $subscription->cancel_at_period_end ? 1 : 0,
                            ]);
                        
                        // Wait for invoice.paid to provision the upgrade
                        Log::info('Waiting for invoice.paid to provision upgrade');
                    } else {
                        // Safe to update immediately (downgrade, metadata change, etc.)
                        Log::info('No payment required - updating immediately');
                        
                        Subscription::where('stripe_subscription_id', $subscription->id)
                            ->update([
                                'plan_id'      => $planId,
                                'status'       => $status,
                                'start_date'   => $subscription->start_date ? date('Y-m-d H:i:s', $subscription->start_date) : null,
                                'end_date'     => $subscription->current_period_end ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                                'cancel_at'    => $subscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                                'cancelled_at' => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null,
                                'reason'       => $subscription->cancellation_details->reason ?? null,
                                'cancel_at_period_end' => $subscription->cancel_at_period_end ? 1 : 0,
                                'pending_plan_id' => null, // Clear any pending changes
                            ]);

                        // Update user's plan immediately for safe changes
                        $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
                        if ($dbSubscription && $dbSubscription->user_id) {
                            $this->updateUserPlan($dbSubscription->user_id, $status, $planId);
                        }
                    }

                    Log::info('Subscription Updated: ' . $subscription->id);
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    Subscription::where('stripe_subscription_id', $subscription->id)
                        ->update([
                            'cancelled_at' => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null,
                            'reason'       => $subscription->cancellation_details->reason ?? null,
                            'status'       => 3,
                            'plan_id'      => null,
                            'pending_plan_id' => null,
                        ]);
                    Log::info('Subscription Cancelled: ' . $subscription->id);

                    $freePlan = MembershipPlan::where('slug', 'free')->first();

                    // Revert user to free plan
                    $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
                    if ($dbSubscription && $dbSubscription->user_id && $freePlan) {
                        $this->updateUserPlan($dbSubscription->user_id, 3, $freePlan->id);
                    }
                    break;

                case 'invoice.paid':
                    $invoice = $event->data->object;
                    $dbSubscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
                    if ($dbSubscription) {
                        $planIdToProvision = $dbSubscription->pending_plan_id ?? $dbSubscription->plan_id;
                        
                        $dbSubscription->update([
                            'status' => 1, // Active
                            'plan_id' => $planIdToProvision,
                            'pending_plan_id' => null,
                        ]);

                        Log::info('Subscription activated with plan', [
                            'subscription_id' => $dbSubscription->id,
                            'plan_id' => $planIdToProvision
                        ]);

                        // NOW provision access to the user
                        if ($dbSubscription->user_id && $planIdToProvision) {
                            $user = User::find($dbSubscription->user_id);
                            if ($user) {
                               $this->updateUserPlan($user->id, 1, $planIdToProvision);
                            }
                        }
                    }

                    // Record the payment
                    Payment::create([
                        'subscription_id'          => $dbSubscription ? $dbSubscription->id : null,
                        'customerID'               => $invoice->customer,
                        'stripe_payment_intent_id' => $invoice->payment_intent ?? $invoice->id,
                        'amount'                   => $invoice->amount_paid / 100,
                        'status'                   => 1, // paid
                    ]);
                    
                    Log::info('Invoice Payment succeeded and access provisioned: ' . $invoice->id);
                    break;

                case 'invoice.payment_failed':
                    $invoice = $event->data->object;
                    $status = 4;
                
                    $dbSubscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
                    if ($dbSubscription) {
                        $dbSubscription->update([
                            'status' => $status, // past_due
                            'pending_plan_id' => null,
                        ]);
                    }

                    $freePlan = MembershipPlan::where('slug', 'free')->first();
                    $this->updateUserPlan($dbSubscription->user_id, $status, $freePlan->id);

                    // Record the failed payment
                    Payment::create([
                        'subscription_id'          => $dbSubscription ? $dbSubscription->id : null,
                        'customerID'               => $invoice->customer,
                        'stripe_payment_intent_id' => $invoice->payment_intent ?? $invoice->id,
                        'amount'                   => $invoice->amount_due / 100,
                        'status'                   => 2, // failed
                    ]);
                    break;

                case 'invoice.payment_action_required':
                    // Payment requires additional action (like 3D Secure)
                    $invoice = $event->data->object;
                    
                    Log::warning('Invoice Payment Action Required', [
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $invoice->subscription
                    ]);

                    $dbSubscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();
                    
                    if ($dbSubscription && $dbSubscription->user_id) {
                        // TODO: Notify user that action is required
                        // Send them the payment confirmation URL
                        Log::info('User needs to complete payment action', [
                            'user_id' => $dbSubscription->user_id,
                            'hosted_invoice_url' => $invoice->hosted_invoice_url
                        ]);
                    }
                    break;

                default:
                    Log::info('Unhandled event type: ' . $event->type);
                    break;
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\UnexpectedValueException $e) {
            Log::error('Webhook Invalid Payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook Invalid Signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook Processing Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Extract user_id from subscription metadata or existing records
     */
    private function getUserIdFromSubscription($subscription)
    {
        // Try metadata first
        if (!empty($subscription->metadata) && isset($subscription->metadata->user_id)) {
            return $subscription->metadata->user_id;
        }

        // Fallback to existing subscription record
        if (!empty($subscription->customer)) {
            $existingSubscription = Subscription::where('stripe_customer_id', $subscription->customer)
                ->whereNotNull('user_id')
                ->first();
            if ($existingSubscription) {
                return $existingSubscription->user_id;
            }
        }

        return null;
    }

    /**
     * Extract plan_id from subscription items
     */
    private function getPlanIdFromSubscription($subscription)
    {
        if (!empty($subscription->items->data[0]->price->id)) {
            $plan = MembershipPlan::where('price_id', $subscription->items->data[0]->price->id)->first();
            if ($plan) {
                return $plan->id;
            }
        }
        return null;
    }

    private function subscriptionUpdateRequiresPayment($subscription, $previousAttributes)
    {
       if (!$previousAttributes) {
            return false;
        }

        if (isset($previousAttributes->items)) {
            $oldPrice = $previousAttributes->items->data[0]->price ?? null;
            $newPrice = $subscription->items->data[0]->price ?? null;

            $oldPriceId = $oldPrice->id ?? null;
            $newPriceId = $newPrice->id ?? null;

            if ($oldPriceId && $newPriceId && $oldPriceId !== $newPriceId) {

                $oldPlan = MembershipPlan::where('price_id', $oldPriceId)->first();
                $newPlan = MembershipPlan::where('price_id', $newPriceId)->first();

                if ($oldPlan && $newPlan) {

                    // Determine billing interval from Stripe
                    $interval = $newPrice->recurring->interval ?? 'monthly';

                    $oldCost = $interval === 'year'
                        ? $oldPlan->annual_cost
                        : $oldPlan->monthly_cost;

                    $newCost = $interval === 'year'
                        ? $newPlan->annual_cost
                        : $newPlan->monthly_cost;

                    // Upgrade requires payment, downgrade doesn't
                    if ($newCost > $oldCost) {
                        Log::info('Upgrade detected - payment required');
                        return true;
                    }

                    Log::info('Downgrade detected - no immediate payment');
                    return false;
                }
            }
        }

        // Status changed from incomplete/past_due to active (payment retry succeeded)
        if (isset($previousAttributes->status)) {
            if (in_array($previousAttributes->status, ['incomplete', 'past_due', 'unpaid']) 
                && $subscription->status === 'active') {
                Log::info('Status changed to active from incomplete state - payment likely succeeded');
                return false; // invoice.paid will handle this
            }
        }

        // Default: no payment required (metadata changes, cancellations, etc.)
        return false;
    }

    /**
     * Update user's plan based on subscription status
     */
    private function updateUserPlan($userId, $status, $planId)
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        if ($status == 1 && $planId) { // Active with valid plan
            $user->plan_id = $planId;
        } else {
            // Inactive - revert to free plan
            $freePlan = MembershipPlan::where('slug', 'free')->first();
            $user->plan_id = $freePlan ? $freePlan->id : 1;
        }
        
        $user->save();

        try {
            $membershipManager = new MembershipManagementService(new CreditService);
            $membershipManager->syncUserCreditsWithPlan($user->id, $user->plan_id);
        } catch (Exception $e) {
            Log::error('Failed to sync credits: ' . $e->getMessage());
        }
    }

    public function createCheckoutSession(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $request->validate(['plan' => 'required|in:grow,explore,thrive']);

        $userId = Auth::id();
        $plan = MembershipPlan::where('slug', $request->plan)->first();
        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        $priceId = $plan->price_id ?? null;
        $appUrl = config('app.url');
        $envUrl = str_replace('.com', '.me', $appUrl);

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $envUrl . '/subscription/success',
            'cancel_url' => $envUrl . '/home', // '/subscription/canceled',
            'client_reference_id' => $userId,
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $userId,
                ],
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }

    public function getCustomerPortal(Request $request)
    {
        $user = Auth::user();

        $subscription = Subscription::where('user_id', $user->id)
            ->whereNotNull('stripe_customer_id')
            ->orderByDesc('id')
            ->first();

        if (!$subscription || !$subscription->stripe_customer_id) {
            return response()->json(['error' => 'Stripe customer_id not found for this user.'], 404);
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $appUrl = config('app.url');
            $envUrl = str_replace('.com', '.me', $appUrl);
            $session = Session::create([
                'customer' => $subscription->stripe_customer_id,
                'return_url' => $envUrl . '/',
            ]);

            Log::info("Stripe session url: " . $session->url);

            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            Log::error('Erro ao criar sessão do portal Stripe: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao gerar link do portal'], 500);
        }
    }

}