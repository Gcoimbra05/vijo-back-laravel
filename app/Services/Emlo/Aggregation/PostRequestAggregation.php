<?php

namespace App\Services\Emlo\Aggregation;

use App\Models\CredScoreInsightsAggregate;
use App\Models\CredScoreValue;
use Exception;
use App\Models\EmloInsightsParamAggregate;
use App\Models\EmloInsightsSecondaryMetric;
use App\Models\EmloResponseParamSpecs;
use App\Models\VideoRequest;
use App\Services\CredScore\CredScoreService;
use App\Services\Emlo\EmloInsights\AveragesService;
use App\Services\Emlo\EmloInsights\EmloInsightsService;
use App\Services\Emlo\EmloResponseService;
use Illuminate\Support\Facades\Log;
use App\Services\Emlo\EmloInsights\InsightsV2Service;
use App\Services\Emlo\EmloInsights\ProgressOverTimeService;
use App\Services\Emlo\EmloHelperService;
use Illuminate\Support\Facades\DB;

class PostRequestAggregation {

    public function __construct(
        protected EmloResponseService $emloResponseService,
        protected ProgressOverTimeService $progressOverTimeService,
        protected InsightsV2Service $insightsV2Service,
        protected AveragesService $averagesService,
        protected EmloInsightsService $emloInsightsService,
        protected CredScoreService $credScoreService){}

    public function aggregationPipeline($requestId, $userId)
    {
        try {
            $paramsWSpec = EmloResponseParamSpecs::select('param_name', 'id', 'needs_normalization')->get();
            foreach ($paramsWSpec as $paramWSpec) {
                    $allValuesOfParam = $this->emloResponseService->getAllRawParamValues($userId, false, $paramWSpec->id);
                    foreach ($allValuesOfParam as $valueOfParam) {
                        if ($valueOfParam->string_value == null && $valueOfParam->numeric_value != null) {
                            $valueOfParam->value = $valueOfParam->numeric_value;
                        } else if ($valueOfParam->string_value != null && $valueOfParam->numeric_value == null) {
                            $valueOfParam->value = $valueOfParam->string_value;
                        } else {
                            $valueOfParam->value = null;
                        }

                        if ($paramWSpec->needs_normalization) {
                            $valueOfParam->value = (int) EmloHelperService::applyNormalizationFormula($valueOfParam->value, $paramWSpec->param_name);
                        }
                    }

                    $last_7_days = $this->averagesService->aggregateData($allValuesOfParam, 'last_7_days');
                    $last_30_days = $this->averagesService->aggregateData($allValuesOfParam, 'last_30_days');
                    $since_start = $this->averagesService->aggregateData($allValuesOfParam, 'since_start');
                    $timeOfDayAverages = $this->averagesService->createTimeOfDayAverages($allValuesOfParam);
                    $last_7_days_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($allValuesOfParam, 'last_7_days');
                    $last_30_days_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($allValuesOfParam, 'last_30_days');
                    $since_start_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($allValuesOfParam, 'since_start');

                    $totalAverage = $this->averagesService->getOverallAverage($allValuesOfParam, 'since_start');

                    $inputData = [
                        'request_id' => $requestId,
                        'emlo_param_spec_id' => $paramWSpec->id,
                        'last_7_days' => json_encode($last_7_days),
                        'last_30_days' => json_encode($last_30_days),
                        'since_start' => json_encode($since_start),
                        'morning' => $timeOfDayAverages['Morning'],
                        'afternoon' => $timeOfDayAverages['Afternoon'],
                        'evening' => $timeOfDayAverages['Evening'],
                        'last_7_days_progress_over_time' => json_encode($last_7_days_progressOverTimeData),
                        'last_30_days_progress_over_time' => json_encode($last_30_days_progressOverTimeData),
                        'since_start_progress_over_time' => json_encode($since_start_progressOverTimeData),
                        'total_average' => $totalAverage
                    ];
                    EmloInsightsParamAggregate::create($inputData);
            }

            $categoryId = DB::table('video_requests')
                ->join('catalogs', 'video_requests.catalog_id', '=', 'catalogs.id')
                ->where('video_requests.id', $requestId)
                ->value('catalogs.category_id');            
            if ($categoryId != 1) {
                $this->credScoreService->processCredScore($requestId, $userId);
            }
        } catch (Exception $e)  {
            Log::error("aggregation pipeline failed w/ error: " . $e->getTraceAsString());
            Log::error("aggregation pipeline failed w/ error message : " . $e->getMessage());
            return ['status' => false, 'error' => 'Interal server error'];   
        }
    }
}