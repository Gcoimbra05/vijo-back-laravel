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

    public static function getPathId($path_key)
    {        
        return self::select('id')
            ->where('path_key', $path_key)
            ->first();
    }
}