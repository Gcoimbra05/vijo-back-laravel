<?php

namespace App\Services\Emlo;

use App\Models\EmloResponse;
use App\Models\EmloResponsePath;
use App\Models\EmloResponseValue;

use Illuminate\Support\Facades\Log;


class EmloHelperService {

    public static function extractAndStorePathValues($rawResponse, $response_id)
    {
        try {

            $decodedResponse = is_string($rawResponse) ? json_decode($rawResponse, true) : $rawResponse;
            if (is_array($decodedResponse) && !isset($decodedResponse[0]) && !empty($decodedResponse)) {
                $decodedResponse = [$decodedResponse];

            }

            $paths = EmloResponsePath::all();
            if (empty($paths)) {
                return [
                    'success' => false,
                    'errors' => 'No path definitions found',
                    'processed' => 0,
                    'stored' => 0
                ];
            }
            
            $processed = 0;
            $stored = 0;
            $errors = [];
            $pathsFound = [];
            
            // Arrays for debugging
            $valueCountByPath = [];
            $pathDetails = [];

            // Process only the predefined paths
            foreach ($paths as $path) {
                $path_id = $path['id'];
                $path_key = $path['path_key'];
                $json_path = $path['json_path'];
                $data_type = $path['data_type'];
                $processed++;
                         
                // Initialize counter for this path
                $valueCountByPath[$path_key] = 0;

                $value = self::extractValueFromResponseByPath($decodedResponse, $json_path);
                if ($value !== null) {
                    $pathsFound[] = $path_key;
                    $isArray = is_array($value);
                    
                    // For debug, store what type of value was found
                    $pathDetails[$path_key] = [
                        'path_id' => $path_id,
                        'json_path' => $json_path,
                        'data_type' => $data_type,
                        'is_array' => $isArray,
                        'array_length' => $isArray ? count($value) : 0,
                        'value_type' => gettype($value)
                    ];
                    
                    // For array values, store the entire array as a JSON string in one record
                    if ($isArray) {
                        $jsonValue = json_encode($value);
                        try {
                            $insert_result = EmloResponseValue::storePathValue($response_id, $path_id, $jsonValue, 'string');
                            if ($insert_result) {
                                $stored++;
                                $valueCountByPath[$path_key]++;

                            } else {
                                $error_msg = "Failed to store value for path_key: {$path_key} (returned false)";
                                $errors[] = $error_msg;

                            }
                        } catch (\Exception $e) {
                            $error_msg = "Exception storing array value for path_key: {$path_key}, Error: " . $e->getMessage();
                            $errors[] = $error_msg;
                        }
                    } else {
                        try {
                            $insert_result = EmloResponseValue::storePathValue($response_id, $path_id, $value, $data_type);                            
                            if ($insert_result) {
                                $stored++;
                                $valueCountByPath[$path_key]++;

                            } else {
                                $error_msg = "Failed to store value for path_key: {$path_key} (returned false)";
                                $errors[] = $error_msg;

                            }
                        } catch (\Exception $e) {
                            $error_msg = "Exception storing value for path_key: {$path_key}, Error: " . $e->getMessage();
                            $errors[] = $error_msg;

                        }
                    }
                }
            }
            // Filter to only show paths with values
            $valueCountByPath = array_filter($valueCountByPath, function($count) {
                return $count > 0;
            });
            
            // Sort by count descending to see which paths generate the most values
            arsort($valueCountByPath);
            
            // Find paths with multiple values
            $multiValuePaths = array_filter($valueCountByPath, function($count) {
                return $count > 1;
            });
                  
            $result = [
                'success' => (count($errors) === 0),
                'processed' => $processed,
                'stored' => $stored,
                'paths_found' => count($pathsFound),
                'multi_value_paths' => count($multiValuePaths),
                'value_counts' => $valueCountByPath,
                'path_details' => $pathDetails,
                'errors' => $errors
            ];
            return $result;
            
    } catch (\Throwable $e) {
        Log::error("Unhandled exception in extractAndStorePathValues", [
            'exception_type' => get_class($e),
            'exception_message' => $e->getMessage(),
            'exception_file' => $e->getFile(),
            'exception_line' => $e->getLine(),
            'exception_trace' => $e->getTraceAsString()
        ]);
        
        // Return error information
        return [
            'success' => false,
            'processed' => 0,
            'stored' => 0,
            'errors' => ['Unhandled exception: ' . $e->getMessage()],
            'exception_type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
}

    private static function extractValueFromResponseByPath($decodedResponse, $path)
    {
        $segments = explode('.', $path);
        $current = $decodedResponse;
        
        foreach ($segments as $segment) {
            // Check if segment is an array index
            if (is_numeric($segment) && is_array($current)) {
                $segment = (int)$segment;
            }
            
            // Check if current segment exists
            if (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
            } else {
                return null; // Path not found
            }
        }
        
        return $current;
    }

    public static function decodeRawResponse($rawResponse) 
    {
        $decodedResponse = is_string($rawResponse) ? json_decode($rawResponse, true) : $rawResponse;
        
        // If it's a direct response (not wrapped in raw_response key)
        if (is_array($decodedResponse) && !array_key_exists('raw_response', $decodedResponse)) {
            return $decodedResponse;
        }
        
        // Handle wrapped response
        if (!empty($decodedResponse['raw_response'])) {
            $decodedResponseData = json_decode($decodedResponse['raw_response']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::debug("Failed to decode raw_response JSON: " . json_last_error_msg());
                return null;
            }
            return $decodedResponseData;
        }
        
        Log::debug("decodedResponse is not in expected format");
        return null;
    }

    public static function validateDecodedResponse($decodedResponseData) {       
        if (!empty($decodedResponseData['data']) && 
            !empty($decodedResponseData['data']['segments']) && 
            !empty($decodedResponseData['data']['segments']['data'])) {
                return true;
            }
        
        return false;
    }

    public static function extractNumericParamValue($valueData) {
        if (!isset($valueData['results']['param_value'][0])) {
            return ['status' => false, 'value' => null];
        }
        
        $paramValue = $valueData['results']['param_value'][0];
        
        // Try numeric_value first, then parse string_value
        if ($paramValue['numeric_value'] !== null) {
            $numericValue = (float) $paramValue['numeric_value'];
            Log::debug('Using numeric_value', ['value' => $numericValue]);
            return ['status' => true, 'value' => $numericValue];
        }
        
        if ($paramValue['string_value'] !== null) {
            $stringValue = (float) $paramValue['string_value'];
            Log::debug('Using string_value converted to numeric', [
                'original' => $paramValue['string_value'],
                'converted' => $stringValue
            ]);
            return ['status' => true, 'value' => $stringValue];
        }
        
        return ['status' => false, 'value' => null];
    }

    public static function applyNormalizationFormula($value) {
        Log::debug('it ran for value: ' . $value);
        $normalized = ($value / 2000) * 100;
        return $normalized;
    }

}