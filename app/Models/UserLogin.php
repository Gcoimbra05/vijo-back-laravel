<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $fillable = [
        'user_id', 'logged_in_at', 'ip_address', 'user_agent'
    ];
}
