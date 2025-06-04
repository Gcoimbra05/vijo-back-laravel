<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rule extends Model
{
    protected $fillable = [
        'name',
        'param_name', 
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function conditions(): HasMany
    {
        return $this->hasMany(RuleCondition::class);
    }

    public function activeConditions(): HasMany
    {
        return $this->conditions()->where('active', true)->orderBy('order_index');
    }
}