<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class KpiMetricSpecification extends Model implements AuditableContract{
    
    use HasFactory, Auditable;

    protected $table = 'kpi_metric_specifications';

    public function credScoreKpi()
    {
        return $this->belongsTo(CredScoreKpi::class, 'kpi_id', 'id');
    }


}