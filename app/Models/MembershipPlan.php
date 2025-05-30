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
        'status',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}