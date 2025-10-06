<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'amount_of_videos',
        'period_type',
        'period_start',
        'period_end',
        'recorded',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
