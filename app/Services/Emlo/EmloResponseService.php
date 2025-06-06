<?php

namespace App\Services\Emlo;

use App\Models\EmloResponse;
use App\Models\EmloResponsePath;
use App\Models\EmloResponseValue;
use Illuminate\Support\Facades\Log;

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
    public function getEmloResponseParamValueForId($request_id, $path_key, $limit = null, $offset = null, $orderColumn = 'created_at', $orderDirection = 'DESC', $start_time = null, $end_time = null)
    {
        try {
            $responseResult = EmloResponse::getEmloResponseByRequestId($request_id);
            if ($responseResult == null || !isset($responseResult['status']) || !$responseResult['status']) {
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
}