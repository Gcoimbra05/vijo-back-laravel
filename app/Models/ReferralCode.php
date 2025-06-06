<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class ReferralCode extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'referral_codes';

    protected $fillable = [
        'affiliate_id',
        'code',
        'commission',
        'max_number_uses',
        'discount',
        'start_date',
        'end_date',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public static function generateReferralCode()
    {
        // Generate random bytes
        $bytes = random_bytes(5); // 5 bytes = 10 hex characters
        
        // Convert to hexadecimal (will be alphanumeric)
        $code = bin2hex($bytes);
        
        // Return uppercase
        return strtoupper($code);
    }
}