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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emlo_response_paths';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get all EMLO response paths
     * 
     * @return array
     */
    public static function getAllEmloResponsePaths()
    {
        return self::select('id', 'path_key', 'json_path', 'data_type')->get()->toArray();
    }

    /**
     * Get path id for given path_key
     * 
     * @param string $path_key
     * @return array Contains path ids matching the path_key
     */
    public static function getEmloResponsePathId($path_key)
    {
        Log::debug('IT IS' . $path_key);
        
        return self::select('id')
            ->where('path_key', $path_key)
            ->get()
            ->toArray();
    }
}