<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class EmailTemplate extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'email_templates';

    protected $fillable = [
        'name',
        'subject',
        'body',
        'status',
        'description',
    ];

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}