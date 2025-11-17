<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeedbackReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_feedback_id',
        'user_id',
        'type',
        'subject',
        'message',
    ];

    public function feedback()
    {
        return $this->belongsTo(UserFeedback::class, 'user_feedback_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

