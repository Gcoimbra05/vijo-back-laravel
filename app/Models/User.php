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
        'mobile',
        'country_code',
        'timezone',
        'email_verified_at',
        'last_login_date',
        'status',
        'is_verified',
        'is_admin',
        'notifications',
        'guided_tours',
        'plan_id',
        'plan_start_date',
        'password',
        'refresh_token',
        'remember_token',
        'notifications',
        'reminders',
        'optInNewsUpdates',
        'description',
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
        'is_admin' => 'boolean',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value && strlen($value) < 60) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

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

    public function trustedDevices()
    {
        return $this->hasMany(TrustedDevice::class);
    }

    public function quickGoals()
    {
        return $this->hasMany(QuickGoal::class);
    }
}