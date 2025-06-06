<?php
// app/Models/EmloResponse.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;


class EmloResponse extends Model implements AuditableContract
{
    use Auditable;

    protected $table = 'emlo_responses';

    protected $fillable = [
        'raw_response',
        'request_id',
    ];

    public static function getEmloResponseByRequestId($request_id)
    {
        $results = self::select('id', 'raw_response', 'created_at')
            ->where('request_id', $request_id)
            ->get();

        // Check if the result is empty
        if ($results->isEmpty()) {
            // No records found
            Log::info('No records found for request_id: ' . $request_id);
            return [
                'success' => false,
                'message' => 'No records found for the specified request ID',
                'data' => []
            ];
        }

        // Records found
        return [
            'success' => true,
            'count' => $results->count(),
            'id' => $results->first()->id
        ];
    }

    
}