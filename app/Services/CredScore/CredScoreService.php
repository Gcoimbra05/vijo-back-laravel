<?php

namespace App\Services\CredScore;

use Exception;
use Illuminate\Support\Facades\Log;

use App\Exceptions\CatalogNotFoundException;
use App\Exceptions\CredScore\CredScoreNotFoundException;

use App\Models\CredScore;
use App\Models\CredScoreKpi;
use App\Models\EmloResponseParamSpecs;
use App\Models\KpiMetricSpecification;
use App\Models\KpiMetricValue;
use App\Models\VideoRequest;
use App\Models\CredScoreValue;
use App\Models\CredScoreInsightsAggregate;

use App\Services\Emlo\EmloDatabaseLoader;
use App\Services\Emlo\EmloInsights\AveragesService;
use App\Services\Emlo\EmloInsights\EmloInsightsService;
use App\Services\Emlo\EmloInsights\ProgressOverTimeService;
use App\Services\Emlo\EmloResponseService;
use App\Services\Rules\RulesEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CredScoreService {

    public function __construct(
        protected EmloResponseService $emloResponseService,
        protected AveragesService  $averagesService,
        protected ProgressOverTimeService $progressOverTimeService,
    ){}

    public function getAllLatestCredScoreData(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $emptyResponse = [
                'status' => 'success',
                'message' => 'Insights data empty',
                'data' => [
                    'vijos' => [
                        'lastMeasured' => '',
                        'profile' => [],
                    ]
                ]
        ];

        $allCredScores = [];
        $catalogsInUse = EmloDatabaseLoader::getMetricCatalogsInUse();

        $credScoreValues = CredScoreValue::select('cred_score_values.cred_score', 'cred_score_values.measured_score', 'cred_score_values.percieved_score','video_requests.catalog_id', 'cred_score_values.created_at')
            ->join('video_requests', 'cred_score_values.request_id', '=', 'video_requests.id')
            ->where('video_requests.user_id', $userId)
            ->get();

        $latestValues = $credScoreValues->groupBy('catalog_id')
            ->map(function($group) {
                return $group->sortByDesc('created_at')->first();
            })
            ->values();

        $lastMeasured = '';
        if ($latestValues && $latestValues->max('created_at')) {
            $lastMeasured = $latestValues->max('created_at')->format('M j, Y');
        }

        $aggregates = CredScoreInsightsAggregate::select(
                'cred_score_insights_aggregates.catalog_id',
                "cred_score_insights_aggregates.since_start",
                'cred_score_insights_aggregates.morning',
                'cred_score_insights_aggregates.afternoon',
                'cred_score_insights_aggregates.evening',
                'cred_score_insights_aggregates.total_average',
                'cred_score_insights_aggregates.since_start_progress_over_time',
                'cred_score_insights_aggregates.created_at',
                'cred_score_insights_aggregates.last_30_days_progress_over_time')
            ->join('video_requests', 'cred_score_insights_aggregates.request_id', '=', 'video_requests.id')
            ->where('video_requests.user_id', $userId)
            ->get();
        if (!$aggregates) {
            Log::info('No EMLO insights aggregate found for user: ' . $userId);
            //return response()->json($emptyResponse);
        }

        $latestAggregates = $aggregates->groupBy('catalog_id')
            ->map(function($group) {
                return $group->sortByDesc('created_at')->first();
            })
            ->values();

        Log::debug("all latestAggregates: " . $latestAggregates);

        Log::debug("catalog in use: " . json_encode($catalogsInUse));

        foreach ($catalogsInUse as $catalogInUse) {
            $aggregatesOfCatalog = $latestAggregates->where('catalog_id', $catalogInUse->id)->first();
            if (!$aggregatesOfCatalog) {
                $allCredScores[] =  $credScoreData = $this->createCredScoresData(
                            $catalogInUse);
                continue;
            }
            Log::debug("aggregates of catalog {$catalogInUse->id}: " . json_encode($aggregatesOfCatalog));

            $latestRecord = $latestValues->where('catalog_id', $catalogInUse->id)->first();
            if ($latestRecord) {
                $latestValue = $latestRecord?->cred_score ?? 0;
                if ($latestValue != 0) {
                    $standardDeviation = RulesEngineService::standardDeviation($latestValues);
                    $statusMessage = $this->ruleCheckCredScore($standardDeviation);
                }

                $timeOfDayAverages = $this->createTimeofDayAverages($aggregatesOfCatalog);
                Log::debug('time of day averages are: ' . json_encode($timeOfDayAverages));

                $weeklyData = $this->averagesService->createWeeklyData($aggregatesOfCatalog);

                $thirtyDayData = $this->averagesService->create30DayData($aggregatesOfCatalog);

                $threeMonthsData = $this->averagesService->aggregateMonthlyData($aggregatesOfCatalog, '3months');
                $sixMonthsData = $this->averagesService->aggregateMonthlyData($aggregatesOfCatalog, '6months');
                $monthsSinceStartData = $this->averagesService->aggregateMonthlyData($aggregatesOfCatalog, 'since_start');

                $credScoreData = $this->createCredScoresData(
                            $catalogInUse, 
                            $aggregatesOfCatalog, 
                            $latestValue, 
                            $statusMessage,
                            $weeklyData,
                            $timeOfDayAverages,
                            $thirtyDayData,
                            $threeMonthsData,
                            $sixMonthsData,
                            $monthsSinceStartData
                            );

                $allCredScores[] = $credScoreData;
            } else {
                $allCredScores[] =  $credScoreData = $this->createCredScoresData(
                            $catalogInUse);
            }
        }
        
        return response()->json([
                'status' => 'success',
                'message' => 'Insights data retrieved successfully',
                'data' => [
                    'vijos' => [
                        'lastMeasured' => $lastMeasured ?? '',
                        'profile' => $allCredScores ?? [],
                    ]
                ]
            ]
        );
    }

    private function createCredScoresData(
        $catalogInUse = null, 
        $aggregate = null, 
        $latest_value = null, 
        $statusMessage = null,
        $weeklyData = null,
        $timeOfDayAverages = null,
        $thirtyDayData = null,
        $threeMonthsData = null,
        $sixMonthsData = null,
        $monthsSinceStartData = null
        ) 
    {

        return [
            "id" => $catalogInUse->id ?? 0,
            "emoji"=> $catalogInUse->emoji ?? "",
            "name"=> $catalogInUse->title ?? "",
            "current" => $latest_value ?? 0,
            "average" => (int) ($aggregate->total_average ?? 0),
            "range" => $statusMessage ?? '',
            "dayChartData" =>  $weeklyData ?? [],
            "timeChartData" => $timeOfDayAverages ?? [],
            "timelineData" => [
                "30days" => $thirtyDayData ?? [],
                "3months" => $threeMonthsData ?? [],
                "6months" => $sixMonthsData ?? [],
                "all" => $monthsSinceStartData ?? []
            ]
        ];
    }

    public function getCredScore($requestId)
    {
        $credScore = CredScoreValue::select('*')
            ->where('request_id', $requestId)
            ->first();
        if (!$credScore) {
            Log::error("Cred score values not found for request {$requestId}");
            return [];
        }

        $returnData = [
            'credScore' => $credScore->cred_score,
            'measuredScore' => $credScore->measured_score,
            'percievedScore' => $credScore->percieved_score,
        ];

        return $returnData;

    }

    public function processCredScore($requestId, $userId)
    {
        $catalogId = VideoRequest::select('catalog_id')
            ->where('id', $requestId)
            ->first();
        if (!$catalogId) {
            throw new CatalogNotFoundException("catalog {$catalogId} not found");
        }

        $credScoreId = CredScore::select('id')
            ->where('catalog_id', $catalogId->catalog_id)
            ->first();
        if (!$credScoreId) {
            throw new CredScoreNotFoundException("cred score for catalog {$catalogId} of request {$requestId} not found");
        }

        $this->storeCredScore($credScoreId, $requestId, $userId);

        $credScoreValues = CredScoreValue::select('cred_score_values.cred_score', 'cred_score_values.measured_score', 'cred_score_values.percieved_score','video_requests.catalog_id', 'cred_score_values.created_at')
            ->join('video_requests', 'cred_score_values.request_id', '=', 'video_requests.id')
            ->where('video_requests.user_id', $userId)
            ->where('video_requests.catalog_id', $catalogId->catalog_id)
            ->get();
            
        Log::Debug("cred score values fetched during POSTAGGRO: " . json_encode($credScoreValues));

        $last_7_days = $this->averagesService->aggregateData($credScoreValues, 'last_7_days', 'cred_score');
        $last_30_days = $this->averagesService->aggregateData($credScoreValues, 'last_30_days','cred_score');
        $since_start = $this->averagesService->aggregateData($credScoreValues, 'since_start', 'cred_score');
        $timeOfDayAverages = $this->averagesService->createTimeOfDayAverages($credScoreValues, 'cred_score');
        $last_7_days_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($credScoreValues, 'last_7_days', 'cred_score');
        $last_30_days_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($credScoreValues, 'last_30_days', 'cred_score');
        $since_start_progressOverTimeData = $this->progressOverTimeService->getProgressOverTimeData($credScoreValues, 'since_start', 'cred_score');
        $totalAverage = $this->averagesService->getOverallAverage($credScoreValues, 'since_start', 'cred_score');

        $inputData = [
            'request_id' => $requestId,
            'catalog_id' => $catalogId->catalog_id,
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
        Log::info("CredScoreInsightsAggregate::create", $inputData);
        CredScoreInsightsAggregate::create($inputData);
    }

    private function storeCredScore($credScoreId, $requestId, $userId) 
    {
        $kpiScores = [];

        $kpis = CredScoreKpi::select('id')
            ->where('cred_score_id', $credScoreId->id)
            ->get();
        if (!$kpis) {
            throw new CredScoreNotFoundException ("Cred score KPIs not found for cred score {$credScoreId->id} of request {$requestId}");
        }

        foreach($kpis as $kpi) {
            $allKpiMetrics = KpiMetricSpecification::select('id', 'range', 'significance', 'emlo_param_spec_id')
                ->where('kpi_id', $kpi->id)
                ->get();
            if (!$allKpiMetrics) {
                throw new CredScoreNotFoundException ("KPI metrics not found for cred score KPI {$kpi->id} of cred score {$credScoreId->id} of request {$requestId}");
            }

            $kpiMetrics = $allKpiMetrics->whereNotNull('significance');
            if (!$kpiMetrics) {
                throw new CredScoreNotFoundException ("KPI metrics with non null significance not found for cred score KPI {$kpi->id} of cred score {$credScoreId->id} of request {$requestId}");
            }
            $kpiScore = $this->calculateKpiScore($kpiMetrics, $requestId, $userId);
            $kpiScores [] = $kpiScore;
        }

        $credScoreValue = array_sum($kpiScores);
        
        $credScore = CredScore::with('catalog')->findOrFail($credScoreId->id);
        $videoTypesId = $credScore->catalog->video_type_id;
        
        if ($videoTypesId != 1) {
            $metricQuestionMetric = $allKpiMetrics
            ->whereNotNull('significance')
            ->whereNull('emlo_param_spec_id')
            ->first();
            if (!$metricQuestionMetric) {
                Log::debug("allKpiMetrics is {$allKpiMetrics}");
                Log::debug("metricQuestionMetric is {$metricQuestionMetric}");
                throw new CredScoreNotFoundException ("KPI metric with non null significance and null emlo_param_spec_id not found for cred score KPI {$kpi->id} of cred score {$credScoreId->id} of request {$requestId}");
            }
        } else {
            $metricQuestionMetric = null;
        }

        $selfHonestyMetric = $allKpiMetrics->where('emlo_param_spec_id', 15)->first();
        if (!$selfHonestyMetric) {
            throw new CredScoreNotFoundException ("selfHonestyMetric KPI metric not found for cred score KPI {$kpi->id} of cred score {$credScoreId->id} of request {$requestId}");
        }
        
        $secondaryScores = $this->getpercievedAndMeasuredScores($requestId, $userId,$metricQuestionMetric,  $selfHonestyMetric);
        $allScores = [
            'percievedScore' => $secondaryScores['percievedScore'] ?? 0,
            'measuredScore' => $secondaryScores['measuredScore'] ?? 0,
            'credScore' => $credScoreValue ?? 0
        ];

        Log::debug("allscores is: " . json_encode($allScores));

        CredScoreValue::create(
            [
                'request_id' => $requestId,
                'cred_score' => $allScores['credScore'],
                'measured_score' => $allScores['measuredScore'],
                'percieved_score' => $allScores['percievedScore']
            ]
        );       
    }

    private function calculateKpiScore($kpiMetrics, $requestId, $userId) 
    {   
            $metricScores = [];
  
            $sumOfSignificances = 0;
            foreach($kpiMetrics as $kpiMetric) {
                $sumOfSignificances += $kpiMetric->significance;
            }
            
            foreach($kpiMetrics as $kpiMetric) {
                $value = $this->getKpiMetricValue($kpiMetric, $requestId, $userId);
                $metricScore = $this->calculateMetricScore($kpiMetric, $value, $sumOfSignificances);
                $metricScores [] = $metricScore;
            }

            $kpiScore = array_sum($metricScores);
            return $kpiScore;
    }

    public function getKpiMetricValue($kpiMetric, $requestId, $userId)
    {
        $value = 0;

        if ($kpiMetric == null) return 0;

        if (isset($kpiMetric->emlo_param_spec_id))  {
            $paramName = EmloResponseParamSpecs::select('param_name')  
                ->where('id', $kpiMetric->emlo_param_spec_id)
                ->first();
            $value = $this->emloResponseService->getParamValueByRequestId($requestId, $userId, $paramName->param_name);
            if (!$value) {
                throw new CredScoreNotFoundException ("KPI EMLO param metric value not found for cred score KPI metric specification {$kpiMetric->id} and request {$requestId}");
            }

            return $value;
        } else {
            $value = KpiMetricValue::select('value')
                        ->where('kpi_metric_spec_id', $kpiMetric->id)
                        ->where('request_id', $requestId)
                        ->first();
            if (!$value) {
                throw new CredScoreNotFoundException ("KPI metric value not found for cred score KPI metric specification {$kpiMetric->id} and request {$requestId}");
            }

            return $value->value;
        }
    }

    private function calculateMetricScore($kpiMetric, $value, $sumOfSignificances)
    {
        if ($sumOfSignificances == 0 || $kpiMetric->range == 0) {
            Log::error("Division by zero in calculateMetricScore: sumOfSignificances={$sumOfSignificances}, range={$kpiMetric->range}");
            return 0;
        }
        $metricScore = (($value - 1)/($kpiMetric->range) * (100-1) + 1) * ($kpiMetric->significance)/$sumOfSignificances;
        return $metricScore;
    }

    private function getpercievedAndMeasuredScores($requestId, $userId, $metricQuestionMetric, $selfHonestyMetric)
    {
        $metricQuestionScore = 0;
        $selfHonestyScore = 0;

        $value = $this->getKpiMetricValue($metricQuestionMetric, $requestId, $userId);
        $metricQuestionScore = $value * 10;
        
        $selfHonestyScore = $this->getKpiMetricValue($selfHonestyMetric, $requestId, $userId);
        
        $result = [
            'percievedScore' => $metricQuestionScore, 
            'measuredScore' => $selfHonestyScore
        ];

        return $result;
    }

    private function ruleCheckCredScore($credScoreValue)
    {
        if ($credScoreValue > -3 && $credScoreValue < -0.68) {
            return "Below Normal";
        } else if ($credScoreValue > -0.67 && $credScoreValue < 0.67) {
            return "Normal";
        } else if ($credScoreValue > 0.67 && $credScoreValue < 3) {
            return "Above Normal";
        } else {
            return "";
        }
    }

    private function createTimeofDayAverages($aggregate = null) 
    {
        return [
            'morning' => (int) ($aggregate->morning ?? 0),
            'afternoon' => (int) ($aggregate->afternoon ?? 0),
            'evening' => (int) ($aggregate->evening ?? 0),
        ];
    }
}