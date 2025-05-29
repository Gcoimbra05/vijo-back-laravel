<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class UserVerification extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'user_verifications';

    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'is_used',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}