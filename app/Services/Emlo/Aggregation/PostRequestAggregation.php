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
use App\Services\Emlo\EmloInsights\SecondaryMetricsService;

class PostRequestAggregation {

    public function __construct(
        protected EmloResponseService $emloResponseService,
        protected ProgressOverTimeService $progressOverTimeService,
        protected InsightsV2Service $insightsV2Service,
        protected SecondaryMetricsService $secondaryMetricsService,
        protected AveragesService $averagesService,
        protected EmloInsightsService $emloInsightsService,
        protected CredScoreService $credScoreService){}

    public function aggregationPipeline($requestId, $userId)
    {
        try {
            $paramsWSpec = EmloResponseParamSpecs::select('param_name', 'id')->get();
            foreach ($paramsWSpec as $paramWSpec) {

                    $result = $this->emloResponseService->getAllValuesOfParam($paramWSpec->param_name, $userId, []);
                    
                    $last_7_days = $this->averagesService->aggregateData($result, 'last_7_days');
                    $last_30_days = $this->averagesService->aggregateData($result, 'last_30_days');
                    $since_start = $this->averagesService->aggregateData($result, 'since_start');
                    $timeOfDayAverages = $this->averagesService->createTimeOfDayAverages($result);
                    $last_7_days_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($result, 'last_7_days');
                    $last_30_days_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($result, 'last_30_days');
                    $since_start_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($result, 'since_start');

                    $totalAverage = $this->averagesService->getOverallAverage($result, 'since_start');

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
                    Log::info("EmloInsightsParamAggregate::create", $inputData);
                    EmloInsightsParamAggregate::create($inputData);
            } 
            $this->credScoreService->processCredScore($requestId, $userId);

        } catch (Exception $e)  {
            Log::error("aggregation pipeline failed w/ error: " . $e->getTraceAsString());
            Log::error("aggregation pipeline failed w/ error message : " . $e->getMessage());
        }
    }
}