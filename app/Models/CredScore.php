<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class CredScore extends Model implements AuditableContract {

    protected $table = 'cred_score';

    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'catalog_id',
    ];
}