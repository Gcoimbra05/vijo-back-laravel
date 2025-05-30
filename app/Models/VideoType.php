<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoType extends Model
{
    use HasFactory;

    protected $table = 'video_types';

    protected $fillable = [
        'name',
        'kpi_no',
        'metric_no',
        'video_no',
        'status',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}