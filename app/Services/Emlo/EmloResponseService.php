<?php

namespace App\Services\Emlo;

use App\Models\EmloResponse;
use App\Models\EmloResponseParamSpecs;
use App\Models\EmloResponsePath;
use App\Models\EmloResponseValue;
use Illuminate\Support\Facades\Log;
use App\Services\Emlo\EmloSegmentParameterService;
use App\Services\QueryParamsHelperService;

use App\Exceptions\Emlo\EmloNotFoundException;




use Exception;

class EmloResponseService
{

    protected $emloSegmentService;

    public function __construct(EmloSegmentParameterService $emloSegmentService)
    {
        $this->emloSegmentService = $emloSegmentService;
    }

    public function getAllValuesOfParam($paramName, $userId, $queryOptions)
    {
        $param = EmloResponseParamSpecs::select('id', 'type', 'needs_normalization', 'path_key')->where('param_name', $paramName)->first();
        if(!$param) {
            throw new EmloNotFoundException('$paramName ' . $paramName . ' does not exist');
        }

        if ($param->type == 'regular') {
            $pathId = $param->path_key ? EmloResponsePath::getPathId($param->path_key) : EmloResponsePath::getPathId($paramName);
            if (!$pathId) {
                throw new EmloNotFoundException("EMLO response path not found for path key '{$param->path_key}'");
            }

            $query = EmloResponseValue::select('response_id', 'path_id', 'numeric_value', 'string_value', 'boolean_value', 'created_at')
                ->where('path_id', $pathId->id)
                ->whereHas('response.request', function ($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                });

            $query = QueryParamsHelperService::applyQueryOptions($query, $queryOptions);
            $responseValues = $query->get();

            $query = QueryParamsHelperService::applyQueryOptions($query, $queryOptions);
            $responseValues = $query->get();

            $formattedResponses = $this->formatResponseValues($responseValues, $paramName);
            if ($formattedResponses->isEmpty()) {
                Log::warning("No formatted responses found for parameter '{$paramName}'");
                return collect(); // Return empty collection instead of throwing exception
            }

            return $formattedResponses;
        } else if ($param->type == 'segment') {
            $responseValues = EmloResponseValue::select('response_id', 'path_id', 'numeric_value', 'string_value', 'boolean_value', 'created_at', 'emlo_param_spec_id')
                ->where('emlo_param_spec_id', $param->id)
                ->whereHas('response.request', function ($subQuery) use ($userId) {
                    $subQuery->where('user_id', $userId);
                })
                ->get();
            
            $formattedResponses = $this->formatResponseValues($responseValues);
            if ($formattedResponses->isEmpty()) {
                Log::warning("No formatted responses found for parameter '{$paramName}'");
                return collect(); // Return empty collection instead of throwing exception
            }
            
            return $formattedResponses;
        }

        return collect(); // Default return empty collection
    }

    public function getParamValueByRequestId($requestId, $userId, $paramName)
    {
        $param = EmloResponseParamSpecs::select('type', 'needs_normalization', 'path_key')
            ->where('param_name', $paramName)
            ->first();
        if(!$param) {
            throw new EmloNotFoundException('$paramName ' . $paramName . ' does not exist');
        }

        $response = EmloResponse::select('id', 'raw_response')
            ->where('request_id', $requestId)
            ->first();

        if(!$response) {
            Log::error("EMLO response not found for request ID: {$requestId}");
            return 0; // Return 0 if response not found
            # throw new EmloNotFoundException("EMLO response not found for request '{$requestId}'");
        }

        if ($param->type == 'regular') {
            $pathId = $param->path_key ? EmloResponsePath::getPathId($param->path_key) : EmloResponsePath::getPathId($paramName);
            if (!$pathId) {
                throw new EmloNotFoundException("EMLO response path not found for path key '{$param->path_key}'");
            }

            $result = EmloResponseValue::select('numeric_value', 'string_value', 'boolean_value', 'created_at')
                ->where('path_id', $pathId->id)
                ->where('response_id', $response->id)
                ->first();
            if (!$result) {
                throw new EmloNotFoundException("EMLO param value not found for path id '{$pathId->id}' and response id '{$response->id}'");
            }

            $array = [
                'results' => [
                    'param_value' => [$result]
                ]
            ];

            $paramValue = EmloHelperService::extractNumericParamValue($array);
            if ($paramValue['status'] == false) {
                throw new Exception("failed to extract numeric value from EMLO parameter");
            }

            $returnValue = $param->needs_normalization ? EmloHelperService::applyNormalizationFormula($paramValue['value']) : $paramValue['value'];
            return $returnValue;

        } else if ($param->type == 'segment') {
            Log::debug('paramName is: ' . $paramName);
            if ($paramName == 'self_honesty') {
                $avg = $this->emloSegmentService->calculateAverageOfSingleResponse('finalRiskLevel', $response->raw_response);
                if ($avg == 1) {
                    return 100;
                }
                $returnValue = 100 - $avg;
                return $returnValue;
            }

            $avg = $this->emloSegmentService->calculateAverageOfSingleResponse($paramName, $response->raw_response);
            $returnValue = $param->needs_normalization ? EmloHelperService::applyNormalizationFormula($avg) : $avg;
            return $returnValue;
        }
    }

    private function formatResponseValues($responseValues)
    {
        //Log::debug("responseValues are: " . json_encode($responseValues));

        $processedRecords = collect(); // Create empty Collection

        foreach ($responseValues as $record) {
            $processedRecord = $this->processRecord($record);
            $processedRecords->push($processedRecord); // Use push() instead of []
        }

        //Log::debug("processedRecords are: " . json_encode($processedRecords));
        return $processedRecords; // Returns Illuminate\Support\Collection
    }

    private function processRecord($record)
    {
        // Get the value (numeric_value or string_value)
        $value = null;
        if ($record->numeric_value !== null) {
            $value = (float)$record->numeric_value;
        } elseif ($record->string_value !== null) {
            $value = (float)$record->string_value;
        }

        // Return in the format aggregateData expects
        return (object) [
            'value' => $value,
            'created_at' => $record->created_at
        ];
    }

    public function storeSegmentParameters($responseId, $rawResponse)
    {
        try {
        $paramsInUse = EmloDatabaseLoader::getParamsInUse();
        $paramsCollection = collect($paramsInUse);
        $segmentParams = $paramsCollection->where('type', 'segment');
        foreach ($segmentParams as $segmentParam) {
            if ($segmentParam->param_name == 'self_honesty') {
                $avg = $this->emloSegmentService->calculateAverageOfSingleResponse('finalRiskLevel', $rawResponse);
                if ($avg == 1) {
                    return 100;
                }
                $returnValue = 100 - $avg;
            } else {
                $avg = $this->emloSegmentService->calculateAverageOfSingleResponse($segmentParam->param_name, $rawResponse);
                $returnValue = $segmentParam->needs_normalization ? EmloHelperService::applyNormalizationFormula($avg) : $avg;

            }

                EmloResponseValue::create(
                    [
                        'response_id' => $responseId,
                        'numeric_value' => $returnValue,
                        'emlo_param_spec_id' => $segmentParam->id,
                    ]
                );
        }
        } catch (Exception $e) {
            Log::debug("error is: " . $e->getMessage());
        }
    }
}
