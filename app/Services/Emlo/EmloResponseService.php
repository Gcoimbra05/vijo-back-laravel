<?php

namespace App\Services\Emlo;

use App\Models\EmloResponse;
use App\Models\EmloResponsePath;
use App\Models\EmloResponseValue;
use Illuminate\Support\Facades\Log;

class EmloResponseService
{
    /**
     * Extract values from JSON response based on predefined paths and store them
     * Only stores values for the predefined paths in the emlo_response_paths table
     * With detailed debugging to track which paths generate multiple values
     * 
     * @param string|array $raw_response Raw JSON response or already decoded array
     * @param int $response_id The ID of the stored API response
     * @return array Result with counts of processed and stored values
     */
    public function extractAndStorePathValues($raw_response, $response_id)
{
    try {
        Log::info("Starting extractAndStorePathValues for response_id: {$response_id}");
        
        // Ensure JSON response is decoded if it's a string
        $decoded_json = is_string($raw_response) ? json_decode($raw_response, true) : $raw_response;
        Log::info("JSON decoded, type: " . gettype($decoded_json));
        
        // Normalize the structure - if it's a direct object, wrap it in an array
        if (is_array($decoded_json) && !isset($decoded_json[0]) && !empty($decoded_json)) {
            $decoded_json = [$decoded_json];
            Log::info("Normalized JSON structure to array");
        }

        Log::info("Fetching defined paths from EmloResponsePath");
        $paths = EmloResponsePath::getAllEmloResponsePaths();
        if (empty($paths)) {
            Log::warning("No path definitions found");
            return [
                'success' => false,
                'errors' => 'No path definitions found',
                'processed' => 0,
                'stored' => 0
            ];
        }
        
        Log::info("Found " . count($paths) . " path definitions");
        
        $processed = 0;
        $stored = 0;
        $errors = [];
        $pathsFound = [];
        
        // Arrays for debugging
        $valueCountByPath = [];
        $pathDetails = [];

        // Delete existing values for this response
        //EmloResponseValue::where('response_id', $response_id)->delete();
        
        Log::info("Beginning to process paths");
        // Process only the predefined paths
        foreach ($paths as $path) {
            $path_id = $path['id'];
            $path_key = $path['path_key'];
            $json_path = $path['json_path'];
            $data_type = $path['data_type'];
            $processed++;
            
            //Log::info("Processing path {$processed}/{" . count($paths) . "}: {$path_key}");
            
            // Initialize counter for this path
            $valueCountByPath[$path_key] = 0;
            
            // Extract value using the json_path
            //Log::info("Extracting value for path: {$json_path}");
            $value = $this->extractValueByPath($decoded_json, $json_path);
            
            if ($value !== null) {
                //Log::info("Value found for path: {$path_key}");
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
                    //Log::info("Value is an array with " . count($value) . " elements");
                    // Convert the entire array to a JSON string
                    $jsonValue = json_encode($value);
                    
                    // Store the entire array as a single string value
                    try {
                        //Log::info("Storing array value as JSON string for path: {$path_key}");
                        $insert_result = EmloResponseValue::storePathValue($response_id, $path_id, $jsonValue, 'string');
                        
                        if ($insert_result) {
                            $stored++;
                            $valueCountByPath[$path_key]++;
                            //Log::info("Successfully stored array value for path: {$path_key}");
                        } else {
                            $errors[] = "Failed to store value for path_key: {$path_key} (returned false)";
                            //Log::warning("Failed to store array value for path: {$path_key}");
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Exception storing array value for path_key: {$path_key}, Error: " . $e->getMessage();
                        Log::error("Failed to store array value for {$path_key}: " . $e->getMessage());
                    }
                } else {
                    //Log::info("Value is a scalar of type: " . gettype($value));
                    try {
                        //Log::info("Storing scalar value for path: {$path_key}");
                        $insert_result = EmloResponseValue::storePathValue($response_id, $path_id, $value, $data_type);
                        if ($insert_result) {
                            $stored++;
                            $valueCountByPath[$path_key]++;
                            //Log::info("Successfully stored scalar value for path: {$path_key}");
                        } else {
                            $errors[] = "Failed to store value for path_key: {$path_key} (returned false)";
                            //Log::warning("Failed to store scalar value for path: {$path_key}");
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Exception storing value for path_key: {$path_key}, Error: " . $e->getMessage();
                        Log::error("Failed to store scalar value for {$path_key}: " . $e->getMessage());
                    }
                }
            } else {
                Log::info("No value found for path: {$path_key}");
            }
        }
        
        //Log::info("All paths processed. Beginning post-processing");
        
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
        
        // Log the detailed results
        $logOutput = [
            'Processed paths' => $processed,
            'Total values stored' => $stored,
            'Paths with values' => count($pathsFound),
            'Paths with multiple values' => count($multiValuePaths),
        ];
        
        // Convert to JSON for clean logging
        //Log::info('Extraction summary: ' . json_encode($logOutput));
        
        // Log the multi-value paths with details
        if (!empty($multiValuePaths)) {
            $multiValueDetails = [];
            foreach ($multiValuePaths as $path => $count) {
                $multiValueDetails[$path] = [
                    'count' => $count,
                    'details' => $pathDetails[$path] ?? []
                ];
            }
            Log::info('Multi-value paths: ' . json_encode($multiValueDetails));
        }
        
        // Log the top 10 paths by value count
        $topPaths = array_slice($valueCountByPath, 0, 10, true);
        Log::info('Top 10 paths by value count: ' . json_encode($topPaths));
        
        Log::info("extractAndStorePathValues completed successfully");
        return [
            'success' => (count($errors) === 0),
            'processed' => $processed,
            'stored' => $stored,
            'paths_found' => count($pathsFound),
            'multi_value_paths' => count($multiValuePaths),
            'value_counts' => $valueCountByPath,
            'path_details' => $pathDetails,
            'errors' => $errors
        ];
    } catch (\Throwable $e) {
        // Log the exception
        Log::error('Exception in extractAndStorePathValues: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
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
    
    /**
     * Extract a value from a nested array using a dot notation path
     * 
     * @param array $data The array to extract from
     * @param string $path The path in dot notation (e.g., "results.0.name")
     * @return mixed|null The extracted value or null if not found
     */
    protected function extractValueByPath($data, $path)
    {
        $segments = explode('.', $path);
        $current = $data;
        
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

    /**
     * Get EMLO response parameter values for a specific path key
     */
    public function getEmloResponseParamValue($path_key, $filters = null, $limit = null, $offset = null, $orderColumn = 'created_at', $orderDirection = 'DESC', $start_time = null, $end_time = null)
    {
        try {
            $result = EmloResponsePath::getEmloResponsePathId($path_key);
            if (empty($result)) {
                return [
                    'success' => false,
                    'message' => 'EMLO response param not found'
                ];
            }

            $path_id = $result[0]['id'];
            Log::debug('the $path_id is' . $path_id);

            $query = EmloResponseValue::select('response_id', 'path_id', 'numeric_value', 'string_value', 'boolean_value', 'created_at')
                ->where('path_id', $path_id);
            
            // Apply filters for API response ID if provided
            if ($filters) {
                $filters = array_filter($filters); // Remove empty values
                foreach ($filters as $key => $value) {
                    $query->where($key, $value);
                }
            }
            
            // Apply date range filters if provided
            if ($start_time) {
                $query->where('created_at', '>=', $start_time);
            }
            
            if ($end_time) {
                $query->where('created_at', '<=', $end_time);
            }
            
            // Validate and apply ordering
            $allowedColumns = ['created_at', 'updated_at'];
            $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
            
            // Validate direction
            $orderDirection = strtoupper($orderDirection);
            $orderDirection = in_array($orderDirection, ['ASC', 'DESC']) ? $orderDirection : 'DESC';
            
            $query->orderBy($orderColumn, $orderDirection);
            
            // Calculate total for pagination
            $total = $query->count();
            
            // Apply pagination if provided
            if ($limit !== null) {
                $query->limit($limit);
                
                if ($offset !== null) {
                    $query->offset($offset);
                }
            }
            
            $result = $query->get()->toArray();
            
            return [
                'success' => true,
                'data' => $result,
                'pagination' => [
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve response values: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get EMLO response parameter values for a specific request ID and path key
     */
    public function getEmloResponseParamValueForId($request_id, $path_key, $limit = null, $offset = null, $orderColumn = 'created_at', $orderDirection = 'DESC', $start_time = null, $end_time = null)
    {
        try {
            // Log input parameters
            Log::debug("FUNCTION CALLED with parameters: request_id={$request_id}, path_key={$path_key}, limit={$limit}, offset={$offset}, orderColumn={$orderColumn}, orderDirection={$orderDirection}, start_time={$start_time}, end_time={$end_time}");
            
            $responseResult = EmloResponse::getEmloResponseByRequestId($request_id);
            Log::debug("Response result from getEmloResponseByRequestId: " . json_encode($responseResult));
            
            if ($responseResult == null || !isset($responseResult['success']) || !$responseResult['success']) {
                Log::error("EMLO response not found for request_id: {$request_id}");
                return [
                    'success' => false,
                    'message' => 'EMLO response not found for request_id'
                ];
            }
            
            // Debug the structure of responseResult
            Log::debug("responseResult structure: " . print_r($responseResult, true));
            
            // Log whether we have an ID
            Log::debug("responseResult['id'] exists: " . (isset($responseResult['id']) ? 'YES' : 'NO'));
            
            $result = EmloResponsePath::getEmloResponsePathId($path_key);
            Log::debug("Path result from getEmloResponsePathId: " . json_encode($result));
            
            if (empty($result)) {
                Log::error("EMLO response param not found for path_key: {$path_key}");
                return [
                    'success' => false,
                    'message' => 'EMLO response param not found'
                ];
            }

            $path_id = $result[0]['id'];
            Log::debug("The path_id is: {$path_id}");
            
            // Determine the response_id to use
            $response_id = $responseResult['id'];
            Log::debug("Using response_id: {$response_id}");
            
            $query = EmloResponseValue::select('numeric_value', 'string_value', 'boolean_value', 'created_at')
                ->where('path_id', $path_id)
                ->where('response_id', $response_id);
            
            Log::debug("Added where conditions: path_id = {$path_id}, response_id = {$response_id}");
            
            // Apply date range filters if provided
            if ($start_time) {
                $query->where('created_at', '>=', $start_time);
                Log::debug("Added date filter: created_at >= {$start_time}");
            }
            
            if ($end_time) {
                $query->where('created_at', '<=', $end_time);
                Log::debug("Added date filter: created_at <= {$end_time}");
            }
            
            // Validate and apply ordering
            $allowedColumns = ['created_at', 'updated_at'];
            $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
            
            // Validate direction
            $orderDirection = strtoupper($orderDirection);
            $orderDirection = in_array($orderDirection, ['ASC', 'DESC']) ? $orderDirection : 'DESC';
            
            $query->orderBy($orderColumn, $orderDirection);
            Log::debug("Added order by: {$orderColumn} {$orderDirection}");
            
            // Get total count for pagination
            $total = $query->count();
            
            // Apply pagination if provided
            if ($limit !== null) {
                $query->limit($limit);
                
                if ($offset !== null) {
                    $query->offset($offset);
                }
                Log::debug("Added pagination: limit {$limit}, offset {$offset}");
            }
            
            // Log the final SQL query (if available)
            Log::debug("Final SQL query: " . $query->toSql());
            
            $result = $query->get()->toArray();
            Log::debug("Query returned " . count($result) . " results");
            
            // Log sample result if available
            if (!empty($result)) {
                Log::debug("First result: " . json_encode($result[0]));
            }
            
            return [
                'success' => true,
                'data' => $result,
                'pagination' => [
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ];
        } catch (\Exception $e) {
            Log::error("Exception caught: " . $e->getMessage());
            Log::error("Exception trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Failed to retrieve response values: ' . $e->getMessage()
            ];
        }
    }

    /*
    public function getFeelGPTWTranscript($request_id){
         // Log input parameters
        Log::debug("FUNCTION CALLED with parameters: request_id={$request_id}");
        
        $resultArr = EmloResponse::getEmloResponseByRequestId($request_id);
        Log::debug("Response result from getEmloResponseByRequestId: " . json_encode($resultArr));
        
        if ($resultArr == null || !isset($resultArr['success']) || !$resultArr['success']) {
            Log::error("EMLO response not found for request_id: {$request_id}");
            return [
                'success' => false,
                'message' => 'EMLO response not found for request_id'
            ];
        }

        if (!empty($resultArr->response->data) && !empty($resultArr->response->data->segments) && !empty($resultArr->response->data->segments->data)) {
            Log::info( 'Number of segments to process: ' . count($resultArr->response->data->segments->data));
            
            foreach ($resultArr->response->data->segments->data as $segment_index => $row) {
                Log::info('Processing segment #' . $segment_index);
                
                // Initialize an empty data array for this segment's data
                $transcript_pieces = [];

            
    }
}}
    */


}