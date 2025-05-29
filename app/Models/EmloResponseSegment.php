<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class EmloResponseSegment extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'emlo_response_segments';

    protected $fillable = [
        'number',
        'name',
    ];

    public $timestamps = true;
}