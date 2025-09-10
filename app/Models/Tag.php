<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'type',
        'created_by_user',
        'status',
    ];

    // Relação com a categoria
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relação com o usuário que criou
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user');
    }
}
