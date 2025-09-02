<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class CredScoreValue extends Model implements AuditableContract{
    
    use HasFactory, Auditable;
    protected $fillable = [
        'request_id',
        'cred_score',
        'percieved_score',
        'measured_score'
    ];

    protected $table = 'cred_score_values';
}