<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;

class Transcript extends Model implements AuditableContract {

    use Auditable;

    protected $fillable = [
        'request_id',
        'text',
        'text_w_segment_emotions',
    ];

}