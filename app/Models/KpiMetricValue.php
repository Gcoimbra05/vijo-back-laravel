<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class KpiMetricValue extends Model implements AuditableContract{
    
    use HasFactory, Auditable;

    protected $table = 'kpi_metric_values';
}