<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class CredScoreInsightsAggregate extends Model implements AuditableContract{
    
    use HasFactory, Auditable;

    protected $fillable = [
        'request_id',
        'catalog_id',
        'last_7_days',
        'last_30_days',
        'since_start',
        'morning',
        'afternoon',
        'evening',
        'last_7_days_progress_over_time',
        'last_30_days_progress_over_time',
        'since_start_progress_over_time',
        'total_average'
    ];

    protected $table = 'cred_score_insights_aggregates';
}