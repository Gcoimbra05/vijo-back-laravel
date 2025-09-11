<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MembershipPlan extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'membership_plans';

    protected $fillable = [
        'name', #text OK
        'slug', #text output OK
        'description', #text input OK
        'payment_mode', #tinyint select
        'monthly_cost', #number double
        'annual_cost', #number double
        'payment_link', #text input OK
        'status', #tinyint select OK
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

}
