<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CreditTransaction;
use App\Models\UserCredit;
use App\Models\User;
use App\Models\MembershipPlan;
use Exception;

class CreditService
{
    /**
     * Initialize credits for a new user or plan change
     */
    public function initializeCredits(User $user, MembershipPlan $plan): UserCredit
    {
        return DB::transaction(function () use ($user, $plan) {
            // Create or update user credits
            $userCredit = UserCredit::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'general_credit_balance' => $plan->general_user_credits,
                    'ai_credit_balance' => $plan->ai_user_credits
                ]
            );

            // Log initial credit allocation
            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $plan->general_user_credits,
                'balance_after' => $userCredit->general_credit_balance,
                'type' => 'credit',
                'reference' => 'Plan allocation',
                'credit_type' => 'general_credits'
            ]);

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $plan->ai_user_credits,
                'balance_after' => $userCredit->ai_credit_balance,
                'type' => 'credit',
                'reference' => 'Plan allocation',
                'credit_type' => 'ai_credits'
            ]);

            Log::info('Credits initialized', [
                'user_id' => $user->id,
                'general_credits' => $plan->general_user_credits,
                'ai_credits' => $plan->ai_user_credits
            ]);

            return $userCredit;
        });
    }

    /**
     * Consume general credits
     */
    public function consumeGeneralCredits(User $user, int $amount, string $reference = 'Video recording'): bool
    {
        return DB::transaction(function () use ($user, $amount, $reference) {
            $userCredit = UserCredit::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$userCredit || $userCredit->general_credit_balance < $amount) {
                Log::warning('Insufficient general credits', [
                    'user_id' => $user->id,
                    'requested' => $amount,
                    'available' => $userCredit ? $userCredit->general_credit_balance : 0
                ]);
                return false;
            }

            $userCredit->general_credit_balance -= $amount;
            $userCredit->save();

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'balance_after' => $userCredit->general_credit_balance,
                'type' => 'debit',
                'reference' => $reference,
                'credit_type' => 'general_credits'
            ]);

            Log::info('General credits consumed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'balance_after' => $userCredit->general_credit_balance,
                'reference' => $reference
            ]);

            return true;
        });
    }

    /**
     * Consume AI credits
     */
    public function consumeAiCredits(User $user, int $amount, string $reference = 'AI analysis'): bool
    {
        return DB::transaction(function () use ($user, $amount, $reference) {
            $userCredit = UserCredit::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$userCredit || $userCredit->ai_credit_balance < $amount) {
                Log::warning('Insufficient AI credits', [
                    'user_id' => $user->id,
                    'requested' => $amount,
                    'available' => $userCredit ? $userCredit->ai_credit_balance : 0
                ]);
                return false;
            }

            $userCredit->ai_credit_balance -= $amount;
            $userCredit->save();

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'balance_after' => $userCredit->ai_credit_balance,
                'type' => 'debit',
                'reference' => $reference,
                'credit_type' => 'ai_credits'
            ]);

            Log::info('AI credits consumed', [
                'user_id' => $user->id,
                'amount' => $amount,
                'balance_after' => $userCredit->ai_credit_balance,
                'reference' => $reference
            ]);

            return true;
        });
    }

    /**
     * Add credits (manual adjustment or bonus)
     */
    public function addCredits(User $user, int $generalAmount, int $aiAmount, string $reference = 'Manual adjustment'): UserCredit
    {
        return DB::transaction(function () use ($user, $generalAmount, $aiAmount, $reference) {
            $userCredit = UserCredit::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ($generalAmount > 0) {
                $userCredit->general_credit_balance += $generalAmount;
                CreditTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $generalAmount,
                    'balance_after' => $userCredit->general_credit_balance,
                    'type' => 'credit',
                    'reference' => $reference,
                    'credit_type' => 'general_credits'
                ]);
            }

            if ($aiAmount > 0) {
                $userCredit->ai_credit_balance += $aiAmount;
                CreditTransaction::create([
                    'user_id' => $user->id,
                    'amount' => $aiAmount,
                    'balance_after' => $userCredit->ai_credit_balance,
                    'type' => 'credit',
                    'reference' => $reference,
                    'credit_type' => 'ai_credits'
                ]);
            }

            $userCredit->save();

            Log::info('Credits added', [
                'user_id' => $user->id,
                'general_amount' => $generalAmount,
                'ai_amount' => $aiAmount,
                'reference' => $reference
            ]);

            return $userCredit;
        });
    }

    /**
     * Reset monthly credits
     */
    public function resetMonthlyCredits(User $user): UserCredit
    {
        $plan = $user->membershipPlan;

        if (!$plan) {
            throw new Exception('User does not have an active plan');
        }

        return DB::transaction(function () use ($user, $plan) {
            $userCredit = UserCredit::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            $userCredit->general_credit_balance = $plan->general_user_credits;
            $userCredit->ai_credit_balance = $plan->ai_user_credits;
            $userCredit->save();

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $plan->general_user_credits,
                'balance_after' => $userCredit->general_credit_balance,
                'type' => 'credit',
                'reference' => 'Monthly reset',
                'credit_type' => 'general_credits'
            ]);

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $plan->ai_user_credits,
                'balance_after' => $userCredit->ai_credit_balance,
                'type' => 'credit',
                'reference' => 'Monthly reset',
                'credit_type' => 'ai_credits'
            ]);

            Log::info('Credits reset for user', [
                'user_id' => $user->id,
                'general_credits' => $plan->general_user_credits,
                'ai_credits' => $plan->ai_user_credits
            ]);

            return $userCredit;
        });
    }

    /**
     * Get credit balance for user
     */
    public function getBalance(User $user): ?UserCredit
    {
        return UserCredit::where('user_id', $user->id)->first();
    }

    /**
     * Check if user can perform action
     */
    public function canPerformAction(User $user, string $actionType, int $cost): bool
    {
        $userCredit = $this->getBalance($user);

        if (!$userCredit) {
            return false;
        }

        return match ($actionType) {
            'general', 'general_credits' => $userCredit->general_credit_balance >= $cost,
            'ai', 'ai_credits' => $userCredit->ai_credit_balance >= $cost,
            default => false
        };
    }

    /**
     * Get transaction history
     */
    public function getTransactionHistory(User $user, string $creditType = '', int $limit = 50)
    {
        $query = CreditTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($creditType) {
            $query->where('credit_type', $creditType);
        }

        return $query->limit($limit)->get();
    }

    // ============ LEGACY METHODS (mantidos para compatibilidade) ============

    public function handleCreditsTransaction(int $userId, CreditTransaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction, $userId) {
            $credits = UserCredit::where('user_id', $userId)->lockForUpdate()->first();
            if (!$credits) {
                throw new Exception("Credits for user {$userId} not found");
            }
            
            // Normalize transaction amount: negative for deduction, positive for addition
            $transaction->amount = strtolower($transaction->type) === 'deduct'
                ? -abs($transaction->amount)
                : abs($transaction->amount);
            
            if ($transaction->credit_type == 'ai_credits') {
                $balance = $credits->ai_credit_balance;
            } else {
                $balance = $credits->general_credit_balance;
            }   

            $updatedBalance = $this->calculateBalance($balance, $transaction->amount, $transaction->type);
            
            if ($updatedBalance === false) {
                throw new Exception("Balance amount after calculation is < 0.");
            }
            
            if ($transaction->credit_type == 'ai_credits') {
                $credits->ai_credit_balance = $updatedBalance;
            } else {
                $credits->general_credit_balance = $updatedBalance;
            }   
            
            $credits->save();

            // Ensure transaction records the correct balance
            $transaction->balance_after = $updatedBalance;
            $transaction->user_id = $userId;
            $transaction->save();
        
            return true;
        });
    }

    private function calculateBalance(int $balance, int $amount, string $type): int|false
    {
        switch (strtolower($type)) {
            case 'deduct':
                if (($balance + $amount) < 0) { // amount is negative
                    return false;
                }
                return $balance + $amount;

            case 'add':
            case 'refund':
                return $balance + $amount;

            default:
                throw new \InvalidArgumentException("Invalid transaction type: $type");
        }
    }

    public function resetUserCreditsToZero($userId)
    {
        $credits = UserCredit::select('general_credit_balance', 'ai_credit_balance')
            ->where('user_id', $userId)
            ->first();
        if (!$credits) {
            throw new Exception("Credits for user {$userId} not found");
        }

        $transaction = new CreditTransaction;
        $transaction->type = 'deduct';
        $transaction->amount = $credits->ai_credit_balance;
        $transaction->reference = 'reset-credits-to-zero';
        $transaction->credit_type = 'ai_credits';

        $this->handleCreditsTransaction($userId, $transaction);

        $transaction = new CreditTransaction;
        $transaction->type = 'deduct';
        $transaction->amount = $credits->general_credit_balance;
        $transaction->reference = 'reset-credits-to-zero';
        $transaction->credit_type = 'general_credits';

        $this->handleCreditsTransaction($userId, $transaction);
    }
}