<?php

namespace App\Services\Emlo;

use App\Models\EmloResponse;
use App\Models\EmloResponsePath;
use App\Models\EmloResponseValue;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\isEmpty;

class EmloResponseService
{   
    /**
     * Get EMLO response parameter values for a specific path key
     */
    public static function getEmloResponseParamValue($path_key, $filters = null, $limit = null, $offset = null, $orderColumn = 'created_at', $orderDirection = 'DESC', $start_time = null, $end_time = null)
    {
        try {
            $result = EmloResponsePath::getEmloResponsePathId($path_key);
            if (empty($result)) {
                return [
                    'status' => false,
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

            Log:info("the result is: " . json_encode($result));
            
            return [
                'status' => true,
                'results' => [
                    'param_value' => $result
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to retrieve response values: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get EMLO response parameter values for a specific request ID and path key
     */
    public static function getEmloResponseParamValueForId($request_id, $path_key, $limit = null, $offset = null, $orderColumn = 'created_at', $orderDirection = 'DESC', $start_time = null, $end_time = null)
    {
        try {
            $responseResult = EmloResponse::getEmloResponseByRequestId($request_id);
            if ($responseResult == null || !isset($responseResult['success']) || !$responseResult['success']) {
                return [
                    'status' => false,
                    'message' => 'EMLO response not found for request_id'
                ];
            }
            
            $result = EmloResponsePath::getEmloResponsePathId($path_key);       
            if (empty($result)) {
                return [
                    'status' => false,
                    'message' => 'EMLO response param not found'
                ];
            }

            $path_id = $result[0]['id'];

            $response_id = $responseResult['id'];
   
            $query = EmloResponseValue::select('numeric_value', 'string_value', 'boolean_value', 'created_at')
                ->where('path_id', $path_id)
                ->where('response_id', $response_id);
                       
            // Apply date range filters if provided
            if ($start_time) {
                $query->where('created_at', '>=', $start_time);
            }
            if ($end_time) {
                $query->where('created_at', '<=', $end_time);
            }
            
            $allowedColumns = ['created_at', 'updated_at'];
            $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
            $orderDirection = strtoupper($orderDirection);
            $orderDirection = in_array($orderDirection, ['ASC', 'DESC']) ? $orderDirection : 'DESC';
            $query->orderBy($orderColumn, $orderDirection);

            // Get total count for pagination
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
                'status' => true,
                'results' => [
                    'param_value' => $result
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to retrieve response values: ' . $e->getMessage()
            ];
        }
    }

    public function getEmloResponseParamGroupValue($requestId, $paramGroup) {
 
        $emloResponseService = app(EmloResponseService::class);
        $formattedParams = [];

        $params = EmloResponsePath::select('path_key')
            ->whereRaw('LOWER(path_key) LIKE LOWER(?)', ['%' . $paramGroup . '%'])
            ->get();
            
        foreach($params as $param) {
            $paramValue = $emloResponseService->getEmloResponseParamValueForId($requestId, $param->path_key);
            
            // Add directly to the array (flat structure)
            $formattedParams[] = [
                'param' => $param->path_key,
                'value' => $paramValue
            ];
        }
        
        usort($formattedParams, function($a, $b) {
            $valueA = $this->extractNumericValue($a['value']);
            $valueB = $this->extractNumericValue($b['value']);
                       
            // Sort descending (highest first)
            return $valueB <=> $valueA;
        });
            
        return $formattedParams;
    }

    private function extractNumericValue($valueData) {
        if (!isset($valueData['results']['param_value'][0])) {
            Log::debug('No param_value found, returning 0');
            return 0;
        }
        
        $paramValue = $valueData['results']['param_value'][0];
        
        // Try numeric_value first, then parse string_value
        if ($paramValue['numeric_value'] !== null) {
            $numericValue = (float) $paramValue['numeric_value'];
            Log::debug('Using numeric_value', ['value' => $numericValue]);
            return $numericValue;
        }
        
        if ($paramValue['string_value'] !== null) {
            $stringValue = (float) $paramValue['string_value'];
            Log::debug('Using string_value converted to numeric', [
                'original' => $paramValue['string_value'],
                'converted' => $stringValue
            ]);
            return $stringValue;
        }
        
        Log::debug('No numeric value found, returning 0');
        return 0;
    }

    public static function calculateParamAverage($paramValue) {
        $sum = 0;
        $validCount = 0;

        foreach($paramValue as $singleParam) {
            $valueToAdd = null;
            
            // Check numeric_value first
            if (!empty($singleParam['numeric_value'])) {
                $valueToAdd = $singleParam['numeric_value'];
            } 
            // Check string_value as fallback
            elseif (!empty($singleParam['string_value'])) {
                $valueToAdd = (float)$singleParam['string_value'];
            }
            
            // Only add if we have a valid value
            if ($valueToAdd !== null) {
                $sum += $valueToAdd;
                $validCount++;
            }
        }

        // Avoid division by zero
        if ($validCount === 0) {
            Log::warning("No valid values found for averaging");
            return null;
        }

        $average = $sum / $validCount;
        Log::info("The count is: " . $validCount);
        Log::info("The average is: " . $average);

        return $average;
    }
}