<?php
// app/Models/EmloResponsePath.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class EmloResponsePath extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'emlo_response_paths';

    protected $fillable = [];

    /**
     * Get path id for given path_key
     * 
     * @param string $path_key
     * @return array Contains path ids matching the path_key
     */
    public static function getEmloResponsePathId($path_key)
    {        
        return self::select('id')
            ->where('path_key', $path_key)
            ->get()
            ->toArray();
    }
}