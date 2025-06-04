<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class CatalogMetricQuestionLabel extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'catalog_metric_question_labels';

    protected $fillable = [
        'title',
        'metricOption1Emoji',
        'metricOption1Text',
        'metricOption3Emoji',
        'metricOption3Text',
        'metricOption5Emoji',
        'metricOption5Text',
        'metricOption7Emoji',
        'metricOption7Text',
        'metricOption9Emoji',
        'metricOption9Text',
        'status',
    ];

    public $timestamps = true;

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}