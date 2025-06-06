<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class VideoRequest extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'video_requests';

    protected $fillable = [
        'user_id',
        'catalog_id',
        'ref_user_id',
        'ref_first_name',
        'ref_last_name',
        'ref_country_code',
        'ref_mobile',
        'ref_email',
        'ref_note',
        'contact_id',
        'group_id',
        'status',
        'error',
        'llm_template_id',
        'is_private',
        'title',
        'tags',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    public function refUser()
    {
        return $this->belongsTo(User::class, 'ref_user_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function group()
    {
        return $this->belongsTo(ContactGroup::class, 'group_id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function latestVideo()
    {
        return $this->hasOne(Video::class, 'request_id')->latestOfMany();
    }
    # escreva um método de delete para excluir o video que está relacionado a esse video request
    public function deleteVideo()
    {
        if ($this->latestVideo) {
            $this->latestVideo->delete();
        }
    }
}