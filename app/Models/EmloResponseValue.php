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

    protected $table = 'emlo_response_values';

    protected $fillable = [
        'response_id',
        'path_id',
        'numeric_value',
        'string_value',
        'boolean_value',
    ];

 
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

    public function response()
    {
        return $this->belongsTo(EmloResponse::class,'response_id', 'id');
    }

}