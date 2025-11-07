<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Affiliate extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'affiliates';

    protected $fillable = [
        'user_id',
        'status',
        'type',
        'creator_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class);
    }
}