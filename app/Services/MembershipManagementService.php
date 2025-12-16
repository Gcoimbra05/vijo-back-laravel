<?php

namespace App\Services;

use App\Models\MembershipPlan;
use App\Models\UserCredit;
use App\Models\CreditTransaction;
use App\Models\User;
use Exception;

class MembershipManagementService {

    public function __construct(protected CreditService $creditService){}

    public function setupFreePlan(User $user)
    {
        $plan = MembershipPlan::where('slug', 'free')->firstOrFail();
        
        $user->plan_id = $plan->id;
        $user->save();
        
        $this->syncUserCreditsWithPlan($user->id, $plan, 'setup-free-plan');
    }

    public function topUpFreePlan(User $user)
    {
        $plan = MembershipPlan::where('slug', 'free')->firstOrFail();        
        $this->syncUserCreditsWithPlan($user->id, $plan, 'setup-free-plan');
    }

    public function syncUserCreditsWithPlan($userId, $plan = null, $reference = null)
    {
        // Allow passing plan object or ID
        if (is_int($plan)) {
            $plan = MembershipPlan::findOrFail($plan);
        }
        
        // Verify user credits exist
        UserCredit::where('user_id', $userId)->firstOrFail();
        
        // Reset to zero
        $this->creditService->resetUserCreditsToZero($userId);
        
        // Define credit types to add
        $creditsToAdd = [
            ['amount' => $plan->ai_user_credits, 'type' => 'ai_credits'],
            ['amount' => $plan->general_user_credits, 'type' => 'general_credits'],
        ];
        
        // Add each credit type
        foreach ($creditsToAdd as $credit) {
            $transaction = new CreditTransaction([
                'type' => 'add',
                'amount' => $credit['amount'],
                'reference' => 'sync-w-membership-plan',
                'credit_type' => $credit['type'],
            ]);
            
            $this->creditService->handleCreditsTransaction($userId, $transaction);
        }
    }
}