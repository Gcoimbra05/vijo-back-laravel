<?php

namespace App\Services\Emlo;

use App\Models\EmloResponsePath;
use App\Models\EmloResponseValue;

use Illuminate\Support\Facades\Log;


class EmloHelperService {

    public static function extractAndStorePathValues($rawResponse, $response_id)
{
    Log::info("Starting extractAndStorePathValues", [
        'response_id' => $response_id,
        'raw_response_type' => gettype($rawResponse),
        'raw_response_length' => is_string($rawResponse) ? strlen($rawResponse) : (is_array($rawResponse) ? count($rawResponse) : 'N/A')
    ]);

    try {
        $decodedResponse = is_string($rawResponse) ? json_decode($rawResponse, true) : $rawResponse;
        
        Log::info("Response decoded", [
            'decoded_type' => gettype($decodedResponse),
            'is_array' => is_array($decodedResponse),
            'json_decode_error' => is_string($rawResponse) ? json_last_error_msg() : 'N/A'
        ]);
        
        if (is_array($decodedResponse) && !isset($decodedResponse[0]) && !empty($decodedResponse)) {
            $decodedResponse = [$decodedResponse];
            Log::info("Wrapped single object in array for consistency");
        }

        $paths = EmloResponsePath::all();
        Log::info("Retrieved paths from database", [
            'path_count' => count($paths),
            'paths_empty' => empty($paths)
        ]);

        if (empty($paths)) {
            Log::warning("No path definitions found in database");
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

        Log::info("Starting path processing loop", ['total_paths' => count($paths)]);

        // Process only the predefined paths
        foreach ($paths as $path) {
            $path_id = $path['id'];
            $path_key = $path['path_key'];
            $json_path = $path['json_path'];
            $data_type = $path['data_type'];
            $processed++;
            
            Log::info("Processing path", [
                'path_id' => $path_id,
                'path_key' => $path_key,
                'json_path' => $json_path,
                'data_type' => $data_type,
                'processed_count' => $processed
            ]);
                            
            // Initialize counter for this path
            $valueCountByPath[$path_key] = 0;
            
            $value = self::extractValueFromResponseByPath($decodedResponse, $json_path);
            
            Log::info("Value extraction result", [
                'path_key' => $path_key,
                'value_found' => $value !== null,
                'value_type' => $value !== null ? gettype($value) : 'null',
                'is_array' => is_array($value),
                'array_count' => is_array($value) ? count($value) : 'N/A'
            ]);
            
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
                
                Log::info("Path details stored", [
                    'path_key' => $path_key,
                    'path_details' => $pathDetails[$path_key]
                ]);
                
                // For array values, store the entire array as a JSON string in one record
                if ($isArray) {
                    $jsonValue = json_encode($value);
                    
                    Log::info("Storing array value", [
                        'path_key' => $path_key,
                        'json_value_length' => strlen($jsonValue),
                        'array_items' => count($value)
                    ]);
                    
                    try {
                        $insert_result = EmloResponseValue::storePathValue($response_id, $path_id, $jsonValue, 'string');
                        
                        Log::info("Array storage attempt completed", [
                            'path_key' => $path_key,
                            'insert_result' => $insert_result,
                            'success' => (bool)$insert_result
                        ]);
                        
                        if ($insert_result) {
                            $stored++;
                            $valueCountByPath[$path_key]++;
                            Log::info("Array value stored successfully", [
                                'path_key' => $path_key,
                                'total_stored' => $stored
                            ]);
                        } else {
                            $error_msg = "Failed to store value for path_key: {$path_key} (returned false)";
                            $errors[] = $error_msg;
                            Log::error("Array storage failed", [
                                'path_key' => $path_key,
                                'error' => $error_msg
                            ]);
                        }
                    } catch (\Exception $e) {
                        $error_msg = "Exception storing array value for path_key: {$path_key}, Error: " . $e->getMessage();
                        $errors[] = $error_msg;
                        Log::error("Exception during array storage", [
                            'path_key' => $path_key,
                            'exception_message' => $e->getMessage(),
                            'exception_file' => $e->getFile(),
                            'exception_line' => $e->getLine()
                        ]);
                    }
                } else {
                    Log::info("Storing scalar value", [
                        'path_key' => $path_key,
                        'value' => $value,
                        'data_type' => $data_type
                    ]);
                    
                    try {
                        $insert_result = EmloResponseValue::storePathValue($response_id, $path_id, $value, $data_type);
                        
                        Log::info("Scalar storage attempt completed", [
                            'path_key' => $path_key,
                            'insert_result' => $insert_result,
                            'success' => (bool)$insert_result
                        ]);
                        
                        if ($insert_result) {
                            $stored++;
                            $valueCountByPath[$path_key]++;
                            Log::info("Scalar value stored successfully", [
                                'path_key' => $path_key,
                                'total_stored' => $stored
                            ]);
                        } else {
                            $error_msg = "Failed to store value for path_key: {$path_key} (returned false)";
                            $errors[] = $error_msg;
                            Log::error("Scalar storage failed", [
                                'path_key' => $path_key,
                                'error' => $error_msg
                            ]);
                        }
                    } catch (\Exception $e) {
                        $error_msg = "Exception storing value for path_key: {$path_key}, Error: " . $e->getMessage();
                        $errors[] = $error_msg;
                        Log::error("Exception during scalar storage", [
                            'path_key' => $path_key,
                            'exception_message' => $e->getMessage(),
                            'exception_file' => $e->getFile(),
                            'exception_line' => $e->getLine()
                        ]);
                    }
                }
            } else {
                Log::info("No value found for path", [
                    'path_key' => $path_key,
                    'json_path' => $json_path
                ]);
            }
        }
        
        Log::info("Path processing completed", [
            'total_processed' => $processed,
            'total_stored' => $stored,
            'paths_with_values' => count($pathsFound),
            'error_count' => count($errors)
        ]);
        
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
        
        Log::info("Final statistics", [
            'value_count_by_path' => $valueCountByPath,
            'multi_value_paths_count' => count($multiValuePaths),
            'multi_value_paths' => array_keys($multiValuePaths)
        ]);
                    
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
        
        Log::info("Function completed successfully", [
            'result' => $result
        ]);
        
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

}