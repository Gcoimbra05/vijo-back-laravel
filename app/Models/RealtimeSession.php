<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class RealtimeSession extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'realtime_sessions';

    protected $fillable = [
        'user_id',
        'openai_session_id',
    ];

}