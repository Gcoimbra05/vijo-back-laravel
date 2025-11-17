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

    // Timestamps
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class);
    }

    public static function getByUser(int $userId)
    {
        return self::where('user_id', $userId)->get();
    }
}