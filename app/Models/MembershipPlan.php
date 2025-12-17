<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class MembershipPlan extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'membership_plans';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'payment_mode',
        'monthly_cost',
        'annual_cost',
        'payment_link',
        'price_id',
        'status',
        'general_user_credits',
        'ai_user_credits',
        'max_recordings',
        'max_storage_vijos',
        'storage_mb',
        'has_ai_personalized_plans',
        'has_full_ai_access',
        'has_exports',
        'is_free',
        'display_order',
        'badge_text',
        'is_recommended'
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'annual_cost' => 'decimal:2',
        'has_ai_personalized_plans' => 'boolean',
        'has_full_ai_access' => 'boolean',
        'has_exports' => 'boolean',
        'is_free' => 'boolean',
        'is_recommended' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

}
