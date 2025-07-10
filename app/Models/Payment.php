<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
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