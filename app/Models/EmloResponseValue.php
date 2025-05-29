<?php
// app/Models/EmloResponseValue.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class EmloResponseValue extends Model implements AuditableContract
{
    use Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'emlo_response_values';

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
        'response_id',
        'path_id',
        'numeric_value',
        'string_value',
        'boolean_value',
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
            'response_id' => 'required',
            'path_id' => 'required',
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
            'response_id.required' => 'response_id is required',
            'path_id.required' => 'path_id is required',
            'array_index.required' => 'array_index is required',
        ];
    }

    /**
     * Store individual EMLO response values
     * 
     * @param int $response_id
     * @param int $path_id
     * @param mixed $value
     * @param string $data_type
     * @return mixed Contains result ID or false boolean flag indicating failure of insertion
     */
    public static function storePathValue($response_id, $path_id, $value, $data_type)
    {
        // Initialize all value fields to null
        $numeric_value = null;
        $string_value = null;
        $boolean_value = null;
        
        // Convert and assign the value based on data_type
        switch (strtolower($data_type)) {
            case 'numeric':
            case 'decimal':
            case 'integer':
            case 'float':
                $numeric_value = is_numeric($value) ? $value : null;
                break;
                
            case 'string':
            case 'text':
                $string_value = (string)$value;
                break;
                
            case 'boolean':
                if (is_bool($value)) {
                    $boolean_value = $value ? 1 : 0;
                } elseif (is_string($value)) {
                    $lower_value = strtolower($value);
                    if (in_array($lower_value, ['true', '1', 'yes', 'y'])) {
                        $boolean_value = 1;
                    } elseif (in_array($lower_value, ['false', '0', 'no', 'n'])) {
                        $boolean_value = 0;
                    }
                } elseif (is_numeric($value)) {
                    $boolean_value = $value ? 1 : 0;
                }
                break;
                
            default:
                // For unknown types, store as string
                $string_value = is_scalar($value) ? (string)$value : json_encode($value);
        }
        
        $data = [
            'response_id' => $response_id,
            'path_id' => $path_id,
            'numeric_value' => $numeric_value,
            'string_value' => $string_value,
            'boolean_value' => $boolean_value,
        ];
        
        // Insert the data
        try {
            $responseValue = new self($data);
            $responseValue->save();
            return $responseValue->id;
            
        } catch (\Exception $e) {
            Log::error('Failed to store path value: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete an EMLO response value
     * 
     * @param int $id
     * @return array Contains 'success' status or 'message' with error
     */
    public static function deleteEmloResponseValue($id)
    {
        $responseValue = self::find($id);
        
        if (!$responseValue) {
            return ['success' => false, 'message' => 'EMLO response not found'];
        }
        
        try {
            $responseValue->delete();
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to delete record: ' . $e->getMessage()];
        }
    }

    /**
     * Get EMLO response parameter values with optional filtering
     * 
     * @param array|null $filters
     * @param int|null $limit
     * @param int|null $offset
     * @param string $orderColumn
     * @param string $orderDirection
     * @return array Contains 'success' status and either 'data' with response values or 'message' with error
     */
    public static function getEmloResponseParamValue($filters = null, $limit = null, $offset = null, $orderColumn = 'created_at', $orderDirection = 'DESC')
    {
        try {
            $query = self::select('response_id', 'path_id', 'numeric_value', 'string_value', 'boolean_value');
            
            if ($filters) {
                $filters = array_filter($filters); // Remove empty values
                $query->where($filters);
            }
            
            $allowedColumns = ['created_at', 'updated_at'];
            $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
            
            // Validate direction
            $orderDirection = strtoupper($orderDirection);
            $orderDirection = in_array($orderDirection, ['ASC', 'DESC']) ? $orderDirection : 'DESC';
            
            $query->orderBy($orderColumn, $orderDirection);
            
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
                'message' => 'Failed to retrieve referral codes: ' . $e->getMessage()
            ];
        }
    }
}