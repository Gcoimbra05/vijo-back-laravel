<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class User extends Authenticatable implements AuditableContract
{
    use HasApiTokens, Notifiable, Auditable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'country_code',
        'mobile',
        'guided_tours',
        'reminders',
        'notifications',
        'timezone',
        'optInNewsUpdates',
        'last_login_date',
        'status',
        'is_verified',
        'plan_id',
        'plan_start_date',
        'refresh_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_date' => 'datetime',
        'plan_start_date' => 'datetime',
        'reminders' => 'boolean',
        'notifications' => 'boolean',
        'optInNewsUpdates' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(MembershipPlan::class, 'plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function affiliates()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function videoRequests()
    {
        return $this->hasMany(VideoRequest::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}