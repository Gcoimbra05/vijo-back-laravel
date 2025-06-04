<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleCondition extends Model
{
    protected $fillable = [
        'rule_id',
        'condition',
        'message',
        'order_index',
        'active'
    ];

    protected $casts = [
        'condition' => 'array',  // Automatically cast JSON to array
        'active' => 'boolean',
    ];

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class);
    }
}