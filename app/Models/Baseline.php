<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Baseline extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'baselines';
    protected $fillable = [
        'user_id',
        'name',
        'starts_at',
        'ends_at'
    ];
}