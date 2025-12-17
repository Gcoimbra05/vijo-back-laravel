<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MembershipPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\UserCredit;
use App\Services\CreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingController extends Controller
{
    protected $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }

    /**
     * GET /api/billing/overview
     * Returns subscription card + credits overview
     */
    public function overview(Request $request)
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 1)
            ->with('membershipPlan')
            ->first();

        $credits = UserCredit::where('user_id', $user->id)->first();
        $plan = $subscription ? $subscription->membershipPlan : $user->membershipPlan;

        if (!$credits && $plan) {
            $credits = $this->creditService->initializeCredits($user, $plan);
        }

        // Ensure credits exist
        if (!$credits) {
            $credits = new UserCredit([
                'general_credit_balance' => 0,
                'ai_credit_balance' => 0
            ]);
        }

        // Calculate credits used
        $generalCreditsUsed = $plan ? $plan->general_user_credits - ($credits->general_credit_balance ?? 0) : 0;
        $aiCreditsUsed = $plan ? $plan->ai_user_credits - ($credits->ai_credit_balance ?? 0) : 0;

        // Calculate usage percentages
        $generalPercentage = $plan && $plan->general_user_credits > 0 
            ? round(($generalCreditsUsed / $plan->general_user_credits) * 100, 2) 
            : 0;
        $aiPercentage = $plan && $plan->ai_user_credits > 0 
            ? round(($aiCreditsUsed / $plan->ai_user_credits) * 100, 2) 
            : 0;

        // Calculate days until reset (assuming monthly reset on subscription renewal)
        $daysUntilReset = 30;
        if ($subscription && $subscription->next_billing_date) {
            $daysUntilReset = Carbon::parse($subscription->next_billing_date)->diffInDays(now());
        } elseif ($subscription && $subscription->start_date) {
            $nextReset = Carbon::parse($subscription->start_date)->addMonth();
            $daysUntilReset = max(0, $nextReset->diffInDays(now()));
        }

        return response()->json([
            'success' => true,
            'subscription' => [
                'plan_name' => $plan ? $plan->name : 'No Plan',
                'plan_badge' => $plan ? $plan->badge_text : null,
                'monthly_cost' => $plan ? $plan->monthly_cost : 0,
                'annual_cost' => $plan ? $plan->annual_cost : 0,
                'is_free' => $plan ? $plan->is_free : false,
                'status' => $subscription ? $this->getStatusText($subscription->status) : 'inactive',
                'next_billing_date' => $subscription ? $subscription->next_billing_date : null,
                'renews_on' => $subscription && $subscription->next_billing_date 
                    ? Carbon::parse($subscription->next_billing_date)->format('M d, Y')
                    : null,
                'started_at' => $subscription ? Carbon::parse($subscription->created_at)->format('M d, Y') : null,
                'period' => $plan ? ($plan->annual_cost > 0 ? 'annual' : 'monthly') : 'monthly',
                'credits' => ($plan ? ($plan->general_user_credits + $plan->ai_user_credits) : 0) . '/' . ($plan ? ($plan->annual_cost > 0 ? 'yr' : 'mo') : 'mo'),
            ],
            'credits' => [
                'general' => [
                    'total' => $plan ? $plan->general_user_credits : 0,
                    'used' => $generalCreditsUsed,
                    'remaining' => $credits ? $credits->general_credit_balance : 0,
                    'percentage_used' => $generalPercentage
                ],
                'ai' => [
                    'total' => $plan ? $plan->ai_user_credits : 0,
                    'used' => $aiCreditsUsed,
                    'remaining' => $credits ? $credits->ai_credit_balance : 0,
                    'percentage_used' => $aiPercentage
                ],
                'reset_info' => [
                    'days_until_reset' => $daysUntilReset,
                    'reset_date' => $subscription && $subscription->next_billing_date 
                        ? Carbon::parse($subscription->next_billing_date)->format('M d, Y')
                        : null
                ]
            ],
            'storage' => [
                'used_mb' => $user->total_storage_used_mb ?? 0,
                'limit_mb' => $plan ? $plan->storage_mb : 0,
                'percentage_used' => $plan && $plan->storage_mb > 0 
                    ? round(($user->total_storage_used_mb / $plan->storage_mb) * 100, 2)
                    : 0
            ]
        ]);
    }

    /**
     * GET /api/billing/plans
     * Returns all available plans with upgrade/downgrade options
     */
    public function plans(Request $request)
    {
        $user = Auth::user();
        $currentPlan = $user->membershipPlan;
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 1)
            ->first();

        $plans = MembershipPlan::where('status', 1)
            ->orderBy('display_order')
            ->get()
            ->map(function ($plan) use ($currentPlan, $user) {
                $isCurrent = $currentPlan && $currentPlan->id === $plan->id;
                $canDowngrade = $this->canDowngradeToPlan($user, $plan);

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'monthly_cost' => $plan->monthly_cost,
                    'annual_cost' => $plan->annual_cost,
                    'period' => $plan->annual_cost > 0 ? 'annual' : 'monthly',
                    'is_free' => $plan->is_free,
                    'badge_text' => $plan->badge_text,
                    'is_recommended' => $plan->is_recommended,
                    'is_current' => $isCurrent,
                    'can_switch' => !$isCurrent && $canDowngrade,
                    'credits' => [
                        'general' => $plan->general_user_credits,
                        'ai' => $plan->ai_user_credits
                    ],
                    'features' => [
                        'max_recordings' => $plan->max_recordings,
                        'max_storage_vijos' => $plan->max_storage_vijos,
                        'storage_mb' => $plan->storage_mb,
                        'has_ai_personalized_plans' => $plan->has_ai_personalized_plans,
                        'has_full_ai_access' => $plan->has_full_ai_access,
                        'has_exports' => $plan->has_exports
                    ],
                    'payment_link' => $plan->payment_link,
                    'price_id' => $plan->price_id
                ];
            });

        return response()->json([
            'success' => true,
            'current_plan_id' => $currentPlan ? $currentPlan->id : null,
            'plans' => $plans
        ]);
    }

    /**
     * GET /api/billing/history
     * Returns payment history with pagination
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 20);

        $payments = Payment::whereHas('subscription', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('subscription.membershipPlan')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $formattedPayments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'date' => Carbon::parse($payment->created_at)->format('M d, Y'),
                'amount' => $payment->amount,
                'plan_name' => $payment->subscription && $payment->subscription->membershipPlan 
                    ? $payment->subscription->membershipPlan->name 
                    : 'Unknown',
                'status' => $this->getPaymentStatusText($payment->status),
                'stripe_payment_intent_id' => $payment->stripe_payment_intent_id,
                'invoice_url' => null
            ];
        });

        return response()->json([
            'success' => true,
            'payments' => $formattedPayments,
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'last_page' => $payments->lastPage()
            ]
        ]);
    }

    /**
     * POST /api/billing/cancel-subscription
     * Cancel active subscription
     */
    public function cancelSubscription(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 1)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            // Update subscription
            $subscription->status = 3; // canceled
            $subscription->cancelled_at = now();
            $subscription->reason = $request->input('reason');
            $subscription->cancel_at_period_end = true;
            $subscription->save();

            // TODO: Cancel in Stripe
            // $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            // $stripe->subscriptions->cancel($subscription->stripe_subscription_id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully',
                'subscription' => [
                    'status' => 'canceled',
                    'cancelled_at' => $subscription->cancelled_at->format('M d, Y'),
                    'access_until' => $subscription->end_date 
                        ? Carbon::parse($subscription->end_date)->format('M d, Y')
                        : null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can downgrade to a specific plan
     */
    private function canDowngradeToPlan(User $user, MembershipPlan $plan): bool
    {
        // Check storage constraint
        if ($plan->storage_mb && $user->total_storage_used_mb > $plan->storage_mb) {
            return false;
        }

        return true;
    }

    /**
     * Get human-readable status text
     */
    private function getStatusText(int $status): string
    {
        return match ($status) {
            1 => 'active',
            2 => 'inactive',
            3 => 'canceled',
            4 => 'past_due',
            5 => 'unpaid',
            default => 'unknown'
        };
    }

    /**
     * Get human-readable payment status
     */
    private function getPaymentStatusText(int $status): string
    {
        return match ($status) {
            1 => 'paid',
            2 => 'failed',
            3 => 'refunded',
            default => 'unknown'
        };
    }
}
