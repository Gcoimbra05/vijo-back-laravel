<?php

namespace App\Http\Controllers;

use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Stripe\Webhook;
use Stripe\Stripe;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Stripe Webhook Received: ' . $request->getContent());
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
            switch ($event->type) {
                case 'customer.subscription.created':
                    $subscription = $event->data->object;

                    // Tente obter o user_id do metadata do Stripe
                    $userId = null;
                    if (!empty($subscription->metadata) && isset($subscription->metadata->user_id)) {
                        $userId = $subscription->metadata->user_id;
                    }
                    // Se não veio no metadata, tente buscar na tabela subscriptions pelo stripe_customer_id
                    if (!$userId && !empty($subscription->customer)) {
                        $existingSubscription = Subscription::where('stripe_customer_id', $subscription->customer)
                            ->whereNotNull('user_id')
                            ->first();
                        if ($existingSubscription) {
                            $userId = $existingSubscription->user_id;
                        }
                    }

                    $planId = null;
                    $plan = MembershipPlan::where('slug', 'vijoplus')->first();
                    if ($plan) {
                        $planId = $plan->id;
                    }
                    /* if (!empty($subscription->items->data[0]->price->id)) {
                        $plan = MembershipPlan::where('slug', $subscription->items->data[0]->price->id)->first();
                        if ($plan) {
                            $planId = $plan->id;
                        }
                    } */

                    Subscription::create([
                        'user_id'                => $userId,
                        'plan_id'                => $planId,
                        'stripe_customer_id'     => $subscription->customer,
                        'stripe_subscription_id' => $subscription->id,
                        'status'                 => 1, // 1: active
                        'start_date'             => $subscription->start_date ? date('Y-m-d H:i:s', $subscription->start_date) : null,
                        'end_date'               => $subscription->current_period_end ? date('Y-m-d H:i:s', $subscription->current_period_end) : null,
                        'cancel_at'              => $subscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                        'cancelled_at'           => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null,
                        'reason'                 => $subscription->cancellation_details->reason ?? null,
                        'cancel_at_period_end'   => $subscription->cancel_at_period_end ? 1 : 0,
                    ]);
                    Log::info('Subscription Created: ' . $subscription->id);

                    if ($userId && $planId) {
                        User::where('id', $userId)->update(['plan_id' => $planId]);
                    }
                    break;

                case 'customer.subscription.updated':
                    $subscription = $event->data->object;

                    $planId = null;
                    $plan = MembershipPlan::where('slug', 'vijoplus')->first();
                    if ($plan) {
                        $planId = $plan->id;
                    }
                    /* if (!empty($subscription->items->data[0]->price->id)) {
                        $plan = MembershipPlan::where('slug', $subscription->items->data[0]->price->id)->first();
                        if ($plan) {
                            $planId = $plan->id;
                        }
                    } */

                    $statusMap = [
                        'active' => 1,
                        'incomplete' => 2,
                        'canceled' => 3,
                        'past_due' => 4,
                        'unpaid' => 5,
                    ];
                    $status = $statusMap[$subscription->status] ?? 2;

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
                        ]);
                    Log::info('Subscription Updated: ' . $subscription->id);

                    // Busca o user_id pela subscription (não pelo user)
                    $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
                    if ($dbSubscription && $dbSubscription->user_id) {
                        $user = User::find($dbSubscription->user_id);
                        if ($user) {
                            if ($status == 1 && $planId) { // 1: active
                                $user->plan_id = $planId;
                            } else {
                                $user->plan_id = 1;
                            }
                            $user->save();
                        }
                    }
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    Subscription::where('stripe_subscription_id', $subscription->id)
                        ->update([
                            'cancelled_at' => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null,
                            'reason'       => $subscription->cancellation_details->reason ?? null,
                            'status'       => 3,
                        ]);
                    Log::error('Subscriptions Cancelled: ' . $subscription->id);

                    // Busca o user_id pela subscription (não pelo user)
                    $dbSubscription = Subscription::where('stripe_subscription_id', $subscription->id)->first();
                    if ($dbSubscription && $dbSubscription->user_id) {
                        $user = User::find($dbSubscription->user_id);
                        if ($user) {
                            $user->plan_id = 1;
                            $user->save();
                        }
                    }
                    break;

                case 'checkout.session.completed':
                    $session = $event->data->object;
                    Subscription::create([
                        'stripe_subscription_id' => $session->subscription,
                        'customerID'             => $session->customer,
                        'user_id'                => $session->client_reference_id,
                        'status'                 => 1,
                    ]);
                    Log::info('Checkout Completed: ' . $session->id);
                    break;

                case 'invoice.payment_succeeded':
                    $invoice = $event->data->object;

                    // Find the subscription by stripe_subscription_id
                    $subscription = Subscription::where('stripe_subscription_id', $invoice->subscription)->first();

                    Payment::create([
                        'subscription_id'         => $subscription ? $subscription->id : null,
                        'customerID'              => $invoice->customer,
                        'stripe_payment_intent_id'=> $invoice->payment_intent ?? $invoice->id,
                        'amount'                  => $invoice->amount_paid / 100, // Stripe sends in cents
                        'status'                  => 1, // 1: paid
                    ]);
                    Log::info('Invoice Payment succeeded: ' . $invoice->id);
                    break;

                default:
                    Log::info('Unhandled event type: ' . $event->type);
                    break;
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }

    public function createCheckoutSession(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $userId = Auth::id();
        $plan = MembershipPlan::where('slug', 'vijoplus')->first();
        if (!$plan) {
            return response()->json(['error' => 'Plan not found'], 404);
        }

        $priceId = $plan->price_id ?? null;
        $envUrl = env('APP_ENV') === 'production' ? 'https://vijo.me' : 'https://test.vijo.me';

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $envUrl . '/subscription/success',
            'cancel_url' => $envUrl . '/subscription/canceled',
            'client_reference_id' => $userId,
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $userId,
                ],
            ],
        ]);

        return response()->json(['url' => $session->url]);
    }
}