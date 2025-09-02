<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class CredScoreKpi extends Model implements AuditableContract{
    
    use HasFactory, Auditable;

    protected $table = 'cred_score_kpis';

    public function credScore()
    {
        return $this->belongsTo(CredScore::class, 'cred_score_id', 'id');
    }
}