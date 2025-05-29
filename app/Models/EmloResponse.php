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

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emlo_responses';

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
    protected $fillable = [
        'raw_response',
        'request_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Get the validation rules for the model.
     *
     * @return array
     */
    public static function validationRules()
    {
        return [
            'raw_response' => 'required',
            'request_id' => 'required'
        ];
    }

    /**
     * Get the validation messages for the model.
     *
     * @return array
     */
    public static function validationMessages()
    {
        return [
            'raw_response.required' => 'Raw response is required',
            'request_id.required' => 'Request id is required',
        ];
    }

    /**
     * Add new EMLO response
     * 
     * @param string $raw_response
     * @param int $request_id
     * @return array Contains 'success' status with 'id' of new response or 'errors' with validation failures
     */
    public static function store($raw_response, $request_id)
    {
        // Log the inputs
        Log::info('store - request_id: ' . $request_id);
                
        // Log the type of raw_response
        Log::info('store - raw_response type: ' . gettype($raw_response));
        
        // If it's an array or object, log its structure
        if (is_array($raw_response) || is_object($raw_response)) {
            $isTopLevelArray = is_array($raw_response) && isset($raw_response[0]);
            Log::info('store - structure: ' . 
                ($isTopLevelArray ? 'Array wrapped [...]' : 'Direct object {...}'));
        }
        
        try {
            $emloResponse = new self([
                'raw_response' => $raw_response,
                'request_id' => $request_id
            ]);
            
            $emloResponse->save();
            
            Log::info('store - Insert successful, new ID: ' . $emloResponse->id);
            
            return [
                'success' => true,
                'id' => $emloResponse->id,
            ];
        } catch (\Exception $e) {
            Log::error('store - Insert failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'errors' => ['exception' => $e->getMessage()]
            ];
        }
    }

    /**
     * Get an EMLO response by ID
     * 
     * @param int $id The EMLO response ID to retrieve
     * @return array Contains 'success' status and either 'data' with the response details or 'message' with error
     */
    public static function show($id)
    {
        $response = self::find($id);
        
        if (!$response) {
            return ['success' => false, 'message' => 'EMLO response not found'];
        }
            
        return [
            'success' => true,
            'data' => $response->toArray()
        ];
    }

    /**
     * Get an EMLO response by request_id
     * 
     * @param int $request_id The request ID to retrieve responses for
     * @return array Contains 'success' status and either data with the response details or 'message' with error
     */
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

    /**
     * Get all EMLO responses with optional filtering
     * 
     * @param int|null $limit Limit results
     * @param int|null $offset Pagination offset
     * @param string $orderColumn Column to order by (default: created_at)
     * @param string $orderDirection Direction to order by (default: DESC)
     * @return array Contains 'success' status and either 'data' with the responses or 'message' with error
     */
    public static function getAllEmloResponses($limit = null, $offset = null, $orderColumn = 'created_at', $orderDirection = 'DESC')
    {
        try {
            $allowedColumns = ['id', 'raw_response', 'created_at'];
            $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
            
            // Validate direction
            $orderDirection = strtoupper($orderDirection);
            $orderDirection = in_array($orderDirection, ['ASC', 'DESC']) ? $orderDirection : 'DESC';
            
            $query = self::select('id', 'raw_response', 'created_at')
                ->orderBy($orderColumn, $orderDirection);
            
            if ($limit !== null) {
                $query->limit($limit);
                
                if ($offset !== null) {
                    $query->offset($offset);
                }
            }
            
            $results = $query->get();
            
            return [
                'success' => true,
                'data' => $results->toArray()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve EMLO responses: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete an EMLO response
     * 
     * @param int $id
     * @return array Contains 'success' status or 'message' with error
     */
    public static function deleteEmloResponse($id)
    {
        $response = self::find($id);
        
        if (!$response) {
            return ['success' => false, 'message' => 'EMLO response not found'];
        }
        
        try {
            $response->delete();
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete record: ' . $e->getMessage()];
        }
    }
}