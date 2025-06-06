<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LlmTemplate extends Model
{
    use HasFactory;

    protected $table = 'llm_templates';

    protected $fillable = [
        'name',
        'user_id',
        'llm',
        'system_prompt',
        'examples',
        'llm_temperature',
        'llm_response_max_length'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}