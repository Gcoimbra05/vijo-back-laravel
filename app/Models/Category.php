<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Category extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'description',
        'emoji',
        'order',
        'status',
    ];

    public function catalogs()
    {
        return $this->hasMany(Catalog::class, 'category_id');
    }
}