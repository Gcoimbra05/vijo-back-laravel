<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class EmloResponseParamSpecs extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'emlo_response_param_specs';

    protected $fillable = [
        'param_name',
        'simplified_param_name',
        'description',
        'min',
        'max',

    ];

    /**
     * Get path id for given path_key
     * 
     * @param string $path_key
     * @return array Contains path ids matching the path_key
     */
    public static function findByParamName($paramName)
    {
        
        return self::select('description', 'min', 'max')
            ->where('param_name', $paramName)
            ->get()
            ->toArray();
    }

}