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
        return $this->hasMany(Video::class, 'request_id');
    }

    public function videoRequestShares()
    {
        return $this->hasMany(VideoRequestShare::class, 'request_id');
    }

    public function latestVideo()
    {
        return $this->hasOne(Video::class, 'request_id')->latestOfMany();
    }

    public function deleteVideo()
    {
        if ($this->latestVideo) {
            $this->latestVideo->delete();
        }
    }

    public function emloInsightsParamAggregates()
    {
        return $this->hasMany(EmloInsightsParamAggregate::class, 'request_id');
    }

    public function credScoreInsightsAggregates()
    {
        return $this->hasMany(CredScoreInsightsAggregate::class, 'request_id');
    }

    public function delete()
    {
        $videos = Video::where('request_id', $this->id)->get();
        foreach ($videos as $video) {
            $video->delete();
        }
        Transcript::where('request_id', $this->id)->delete();
        LlmResponse::where('request_id', $this->id)->delete();
        $emloResponses = EmloResponse::where('request_id', $this->id)->get();
        foreach ($emloResponses as $emloResponse) {
            EmloResponseValue::where('response_id', $emloResponse->id)->delete();
            $emloResponse->delete();
        }
        LlmResponse::where('request_id', $this->id)->delete();
        KpiMetricValue::where('request_id', $this->id)->delete();
        EmloInsightsParamAggregate::where('request_id', $this->id)->delete();
        CredScoreValue::where('request_id', $this->id)->delete();
        CredScoreInsightsAggregate::where('request_id', $this->id)->delete();

        parent::delete();
    }
}