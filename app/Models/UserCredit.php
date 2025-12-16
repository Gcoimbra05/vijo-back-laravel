<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class UserCredit extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'user_credits';

    protected $fillable = [
        'user_id',
        'general_credit_balance',
        'ai_credit_balance'
    ];

    protected $casts = [
        'general_credit_balance' => 'integer',
        'ai_credit_balance' => 'integer',
    ];

    /**
     * Relationship: User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get remaining general credits
     */
    public function getRemainingGeneralCreditsAttribute(): int
    {
        return max(0, $this->general_credit_balance);
    }

    /**
     * Get remaining AI credits
     */
    public function getRemainingAiCreditsAttribute(): int
    {
        return max(0, $this->ai_credit_balance);
    }

    /**
     * Check if user has enough general credits
     */
    public function hasEnoughGeneralCredits(int $amount): bool
    {
        return $this->general_credit_balance >= $amount;
    }

    /**
     * Check if user has enough AI credits
     */
    public function hasEnoughAiCredits(int $amount): bool
    {
        return $this->ai_credit_balance >= $amount;
    }

    /**
     * Get general credits usage percentage
     */
    public function getGeneralCreditsUsagePercentageAttribute(): float
    {
        $plan = $this->user->membershipPlan;
        if (!$plan || $plan->general_user_credits == 0) {
            return 0;
        }

        $used = $plan->general_user_credits - $this->general_credit_balance;
        return round(($used / $plan->general_user_credits) * 100, 2);
    }

    /**
     * Get AI credits usage percentage
     */
    public function getAiCreditsUsagePercentageAttribute(): float
    {
        $plan = $this->user->membershipPlan;
        if (!$plan || $plan->ai_user_credits == 0) {
            return 0;
        }

        $used = $plan->ai_user_credits - $this->ai_credit_balance;
        return round(($used / $plan->ai_user_credits) * 100, 2);
    }
}
