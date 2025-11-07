<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsightsFilter extends Model
{
    use HasFactory;

    protected $table = 'insights_filter';

    protected $fillable = [
        'user_id',
        'title',
        'start_date',
        'end_date',
        'default',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
