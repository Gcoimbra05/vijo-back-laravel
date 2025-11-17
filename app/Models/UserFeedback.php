<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeedback extends Model
{
    use HasFactory;

    protected $table = 'user_feedbacks';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'email',
        'subject',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_RESPONDED = 2;

    public function getStatusLabelAttribute()
    {
        // If there are replies, treat as Responded regardless of stored status
        if ($this->replies()->count() > 0) {
            return 'Responded';
        }

        $labels = [
            self::STATUS_UNREAD => 'Unread',
            self::STATUS_READ => 'Read',
            self::STATUS_RESPONDED => 'Responded',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    public function replies()
    {
        return $this->hasMany(UserFeedbackReply::class, 'user_feedback_id');
    }

}
