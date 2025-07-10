<?php

namespace App\Services\Emlo;

use App\Models\EmloResponse;
use App\Models\EmloResponseSegment;
use App\Services\Emlo\EmloHelperService;
use Exception;

use App\Exceptions\Emlo\EmloNotFoundException;

class EmloSegmentParameterService {

    public function calculateAverageOfSingleResponse(string $parameterName, string $rawResponse)
    {
        $avg = [];

        $parameterIndex = EmloResponseSegment::select('number')
                                            ->where('name', $parameterName)
                                            ->first();
        if (!$parameterIndex) {
            throw new EmloNotFoundException("EMLO param '{$parameterName}' not found");
        }

        $decodedResponseData = EmloHelperService::decodeRawResponse($rawResponse);
        if ($decodedResponseData === null) {
            throw new Exception("failed to decode raw EMLO response");
        }

        if (!EmloHelperService::validateDecodedResponse($decodedResponseData)) {
            throw new Exception("failed validation for raw EMLO response");
        }

        $segments = $decodedResponseData['data']['segments']['data'];
        foreach ($segments as $segment) {
            foreach ($segment as $segmentDataIndex => $segmentData) {
                if ($parameterIndex->number == $segmentDataIndex) {
                    $avg [] = $segmentData;
                }
            }
        }
        
        $avg = round(array_sum($avg) / count($avg));
        return $avg;
    }

    public static function getAveragesForAllResponses(string $parameterName) 
    {
        $parameterIndex = EmloResponseSegment::select('number')
                                            ->where('name', $parameterName)
                                            ->first();
        if (!$parameterIndex) {
            throw new EmloNotFoundException("EMLO segment param '{$parameterName}' not found");
        }

        $results = EmloResponse::select('raw_response', 'created_at')->get();
        if ($results->isEmpty()) {
            throw new Exception("failed to fetch EMLO responses");
        }
        
        $allResponseAverages = collect(); // Store average for each raw response
        
        foreach ($results as $record) {
            $decodedResponseData = EmloHelperService::decodeRawResponse($record->raw_response);
            if (!$decodedResponseData) {
                continue;
            }

            if (!EmloHelperService::validateDecodedResponse($decodedResponseData)) {
                continue;
            }

            $currentResponseValues = []; // Values for this specific raw response
            $segments = $decodedResponseData['data']['segments']['data'];
            
            foreach ($segments as $segment) {
                foreach ($segment as $segmentDataIndex => $segmentData) {
                    if ($segmentDataIndex == $parameterIndex->number) {
                        $currentResponseValues[] = $segmentData;
                    }
                }
            }
            
            // Calculate average for this raw response
            if (!empty($currentResponseValues)) {
                $currentResponseAverage = array_sum($currentResponseValues) / count($currentResponseValues);
                $allResponseAverages->push( (object) [
                    'value' => $currentResponseAverage,
                    'created_at' => $record->created_at
                ]);
            }
        }
        
        return $allResponseAverages;
    }

}