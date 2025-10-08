<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformText extends Model
{
    protected $table = 'platform_texts';

    protected $fillable = [
        'slug',
        'highlight',
        'emoji',
        'title',
        'description',
        'link',
        'location',
    ];
}
