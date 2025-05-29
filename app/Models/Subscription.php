<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Subscription extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'stripe_customer_id',
        'stripe_subscription_id',
        'status',
        'start_date',
        'end_date',
        'cancel_at',
        'cancelled_at',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }
}