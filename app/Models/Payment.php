<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Payment extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'subscription_id',
        'customerID',
        'stripe_payment_intent_id',
        'amount',
        'status',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}