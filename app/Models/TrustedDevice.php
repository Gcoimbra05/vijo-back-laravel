<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_token',
        'device_name',
        'user_agent',
        'trusted_until',
    ];

    protected $dates = [
        'trusted_until',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}