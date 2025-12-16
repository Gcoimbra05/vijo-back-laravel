<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CompletionsApiInteraction extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'completions_api_interactions';

    protected $fillable = [
        'realtime_session_id',
        'api_response',
        'input_tokens',
        'output_tokens',
    ];

}