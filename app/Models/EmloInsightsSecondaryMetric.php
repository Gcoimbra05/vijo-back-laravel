<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmloInsightsSecondaryMetric extends Model {

    protected $fillable = [
        'info_array',
        'request_id',
        'emlo_param_spec_id'
    ];
}