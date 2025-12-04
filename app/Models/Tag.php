<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Tag extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'type',
        'created_by_user',
        'status',
    ];

    public static $type = [
        'catalog',
        'journalTag',
        'custom'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }
}
