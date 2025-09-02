<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EmloInsightsParamAggregate extends Model {

    protected $fillable = [
        'request_id',
        'emlo_param_spec_id',
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
}