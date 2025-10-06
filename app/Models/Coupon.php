<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Coupon extends Model implements AuditableContract
{
    use HasFactory, Auditable;
    
    protected $fillable = [
        'stripe_coupon_id',
        'affiliate_id'
    ];
}