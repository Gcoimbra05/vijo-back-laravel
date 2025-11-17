<?php

namespace App\Services\Emlo\EmloInsights;

use Illuminate\Support\Facades\Log;

use App\Exceptions\Emlo\EmloNotFoundException;
use Throwable;

use App\Services\Emlo\EmloDatabaseLoader;
use App\Services\Emlo\EmloResponseService;
use App\Services\Emlo\EmloHelperService;
use App\Services\Rules\RuleEvaluationService;
use Illuminate\Support\Facades\Auth;

use App\Models\EmloInsightsParamAggregate;

class SnapshotService {

    public function __construct(
        protected EmloInsightsService $emloInsightsService,
        protected RuleEvaluationService $ruleEvaluationService,
        protected EmloHelperService $emloHelperService,
        protected EmloResponseService $emloResponseService
    ) {}

    public function getEmotionalSnapshot($requestId)
    {
        $allSnapshots = [];
        $paramsInUse = null;
        
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['error' => 'user not found'], 404);
            }
            if (is_null($requestId)) {
                throw new EmloNotFoundException("emlo insights not found: request id is missing");
            }
            
            $paramsInUse = EmloDatabaseLoader::getEmotionalSnapshotParams();
            $aggrOfAllParams = EmloInsightsParamAggregate::select('*')
                ->where('request_id', $requestId)
                ->get();
            if (!$aggrOfAllParams) {
                throw new EmloNotFoundException("emlo insights not found for request {$requestId}");
            }
            
            $allValuesOfAllParams = $this->emloResponseService->getAllRawParamValues($userId);
            foreach ($allValuesOfAllParams as $allValuesOfParam) {
                foreach ($allValuesOfParam as $valueOfParam) {
                    if ($valueOfParam->string_value == null && $valueOfParam->numeric_value != null) {
                        $valueOfParam->value = $valueOfParam->numeric_value;
                    } else if ($valueOfParam->string_value != null && $valueOfParam->numeric_value == null) {
                        $valueOfParam->value = $valueOfParam->string_value;
                    } else {
                        $valueOfParam->value = null;
                    }
                }
            }
            
            Log::debug("numbers of params in use:" . count($paramsInUse));
            
            foreach($paramsInUse as $paramInUse) {
                try {  // Inner try-catch for individual param failures
                    $aggrOfParam = $aggrOfAllParams->where('emlo_param_spec_id', $paramInUse->id)->first();
                    $allValuesOfParam = $allValuesOfAllParams->get($paramInUse->id);    
                    $latestValue = $allValuesOfParam?->sortByDesc('created_at')->first()->value;  
                    
                    if ($paramInUse->needs_normalization) {
                        $latestValue = (int) EmloHelperService::applyNormalizationFormula($latestValue, $paramInUse->param_name);
                    
                        if ($allValuesOfParam !== null) {
                            foreach ($allValuesOfParam as $valueOfParam) {
                                $valueOfParam->value = (int) EmloHelperService::applyNormalizationFormula($valueOfParam->value, $paramInUse->param_name);
                            }
                        }
                    }
                
                    $conditionMet = $this->ruleEvaluationService->quickRuleCheck($latestValue, $allValuesOfParam, $paramInUse);
                    $paramSnapshot = $this->createEmotionalSnapshot($paramInUse, $aggrOfParam, $latestValue, $conditionMet);
                    $allSnapshots[] = $paramSnapshot;
                    
                } catch (Throwable $e) {
                    // Individual param failed - log and create default snapshot
                    Log::error('Failed to process param ' . $paramInUse->param_name . ': ' . $e->getMessage());
                    $paramSnapshot = $this->createEmotionalSnapshot($paramInUse, null, null, null);
                    $allSnapshots[] = $paramSnapshot;
                }
            }
            
            return $allSnapshots;
            
        } catch (Throwable $e) {
            // Early failure - create default snapshots for all params if available
            Log::error('Failed to get emotional snapshot: ' . $e->getMessage() . ' at: ' . $e->getTraceAsString());
            
            if ($paramsInUse !== null) {
                foreach ($paramsInUse as $paramInUse) {
                    $paramSnapshot = $this->createEmotionalSnapshot($paramInUse, null, null, null);
                    $allSnapshots[] = $paramSnapshot;
                }
            }
            
            return $allSnapshots;
        }
    }

    private function createEmotionalSnapshot(
        $paramInUse = null,
        $aggrOfParam = null,
        $latestParamValue = null,
        $conditionMet = null)
    {
        return [
            'value' => $latestParamValue ?? 0,
            'average' => $aggrOfParam?->total_average ?? 0,
            'emotion' => $paramInUse?->simplified_param_name ?? '',
            'description' => $paramInUse?->short_description ?? '',
            'emoji' => $paramInUse?->emoji ?? '',
            'status_message' => $conditionMet?->emotion_performance ?? ''
        ];
    }
}