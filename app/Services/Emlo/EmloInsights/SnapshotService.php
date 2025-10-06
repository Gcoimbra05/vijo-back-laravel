<?php

namespace App\Services\Emlo\EmloInsights;

use Illuminate\Support\Facades\Log;

use App\Exceptions\Emlo\EmloNotFoundException;
use Exception;

use App\Services\Emlo\EmloDatabaseLoader;
use App\Services\Emlo\EmloHelperService;
use App\Services\Rules\RuleEvaluationService;

use App\Models\EmloInsightsParamAggregate;


class SnapshotService {

        public function __construct(
        protected EmloInsightsService $emloInsightsService,
        protected RuleEvaluationService $ruleEvaluationService,
        protected EmloHelperService $emloHelperService){}

    public function getEmotionalSnapshot($requestId)
    {
        try {
            $paramsInUse = EmloDatabaseLoader::getEmotionalSnapshotParams();

            if (is_null($requestId)) {
                throw new EmloNotFoundException("emlo insights not found: request id is missing");
            }

            $aggrOfAllParams = EmloInsightsParamAggregate::select('*')
                ->where('request_id', $requestId)
                ->get();
            if (!$aggrOfAllParams) {
                throw new EmloNotFoundException("emlo insights not found for request {$requestId}");
            }

            $latestValues = $this->emloInsightsService->getRawParamValues($requestId);

            $allSnapshots = [];

            foreach($paramsInUse as $paramInUse) {
                $aggrOfParam = $aggrOfAllParams->where('emlo_param_spec_id', $paramInUse->id)->first();

                $latestValue = $latestValues->firstWhere('emlo_param_spec_id', $paramInUse->id)?->value ?? 0;
                $latestValue = (int) ($paramInUse->needs_normalization ? EmloHelperService::applyNormalizationFormula($latestValue, $paramInUse->param_name) : $latestValue);

                if ($latestValue != 0) {
                    $conditionMet = $this->ruleEvaluationService->quickRuleCheck($latestValue, $latestValues, $paramInUse);
                } else if ($latestValue == 0 && $paramInUse->param_name == 'Aggression') {
                    $conditionMet = $this->ruleEvaluationService->quickRuleCheck($latestValue, $latestValues, $paramInUse);
                }

                $paramSnapshot = $this->createEmotionalSnapshot($paramInUse, $aggrOfParam, $latestValue, $conditionMet);
                $allSnapshots [] = $paramSnapshot;
            }

            return $allSnapshots;
        } catch (Exception $e) {
            foreach ($paramsInUse as $paramInUse) {
                Log::debug('Failed to get emotional-snapshot w/ exception:' . $e->getMessage() . 'at: ' . $e->getTraceAsString());
                $paramSnapshot = $this->createEmotionalSnapshot($paramInUse);
                $allSnapshots [] = $paramSnapshot;
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