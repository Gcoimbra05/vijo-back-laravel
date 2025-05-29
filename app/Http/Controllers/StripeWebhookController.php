<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\Payment;
use Stripe\Webhook;
use Stripe\Stripe;

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

            switch ($event->type) {
                case 'customer.subscription.updated':
                    $subscription = $event->data->object;
                    Subscription::where('stripe_subscription_id', $subscription->id)
                        ->update([
                            'cancel_at'    => $subscription->cancel_at ? date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                            'cancelled_at' => $subscription->canceled_at ? date('Y-m-d H:i:s', $subscription->canceled_at) : null,
                            'reason'       => $subscription->cancellation_details->reason ?? null,
                            'status'       => 1,
                        ]);
                    Log::info('Subscription Updated: ' . $subscription->id);
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
                    $payment = Payment::create([
                        'subscription_id'         => $invoice->subscription,
                        'customerID'              => $invoice->customer,
                        'stripe_payment_intent_id'=> $invoice->id,
                        'amount'                  => $invoice->amount_paid / 100,
                        'status'                  => 1,
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
}