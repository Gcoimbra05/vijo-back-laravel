<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoRequestShare extends Model
{
    use HasFactory;

    protected $table = 'video_request_share';

    protected $fillable = [
        'request_id',
        'sender_user_id',
        'recipient_user_id',
        'first_name',
        'last_name',
        'country_code',
        'mobile',
        'email',
        'note',
        'contact_id',
        'group_id',
        'status',
    ];

    public function request()
    {
        return $this->belongsTo(VideoRequest::class, 'request_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function group()
    {
        return $this->belongsTo(ContactGroup::class, 'group_id');
    }
}
