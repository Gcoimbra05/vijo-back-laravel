<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class CreditTransaction extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'credit_transactions';

    protected $fillable = [
        'user_id',
        'amount',
        'balance_after',
        'type',
        'reference',
        'credit_type'
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    /**
     * Relationship: User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: General credits only
     */
    public function scopeGeneral($query)
    {
        return $query->where('credit_type', 'general');
    }

    /**
     * Scope: AI credits only
     */
    public function scopeAi($query)
    {
        return $query->where('credit_type', 'ai');
    }

    /**
     * Scope: Recent transactions
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
