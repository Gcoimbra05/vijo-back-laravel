<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CatalogAnswer extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'catalog_answers';

    protected $fillable = [
        'user_id',
        'catalog_id',
        'request_id',
        'cred_score',
        'metric1_answer',
        'metric1Range',
        'metric1Significance',
        'metric2_answer',
        'metric2Range',
        'metric2Significance',
        'metric3_answer',
        'metric3Range',
        'metric3Significance',
        'n8n_executionId',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class);
    }

    public function request()
    {
        return $this->belongsTo(VideoRequest::class, 'request_id');
    }
}