<?php

namespace App\Services\Emlo\EmloInsights;

use App\Exceptions\CredScore\CredScoreNotFoundException;
use App\Exceptions\Emlo\EmloNotFoundException;
use App\Models\CredScoreInsightsAggregate;
use App\Models\CredScoreValue;
use App\Models\EmloInsightsParamAggregate;
use App\Models\EmloInsightsSecondaryMetric;
use App\Models\EmloResponseParamSpecs;
use App\Models\EmloResponseValue;
use App\Models\EmloResponse;
use App\Models\VideoRequest;
use App\Services\CredScore\CredScoreService;
use App\Services\Emlo\EmloDatabaseLoader;
use App\Services\Emlo\EmloInsights\AveragesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\Emlo\EmloResponseService;
use App\Services\Emlo\EmloHelperService;
use Illuminate\Support\Facades\Auth;

use App\Services\Emlo\EmloInsights\InsightsV2Service;
use App\Services\Emlo\EmloInsights\ProgressOverTimeService;
use App\Services\Emlo\EmloInsights\SecondaryMetricsService;
use App\Services\Rules\RuleEvaluationService;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Route;

class EmloInsightsService
{
    public function __construct(
        protected EmloResponseService $emloResponseService,
        protected ProgressOverTimeService $progressOverTimeService,
        protected InsightsV2Service $insightsV2Service,
        protected SecondaryMetricsService $secondaryMetricsService,
        protected AveragesService $averagesService,
        protected CredScoreService $credScoreService,
        protected RuleEvaluationService $ruleEvaluationService){}

    public function getInsightsDataV2(Request $request)
    {
        $routeName = Route::currentRouteName();
        if ($routeName == 'api.v2.insights.v2') {
            $paramsInUse = EmloDatabaseLoader::getEdpParamsInUse();
            $dataSubSection = 'emotions';
        } else if ($routeName == 'api.v2.insights.v2.secondary-metrics') {
            $paramsInUse = EmloDatabaseLoader::getSecondaryMetricParams();
            $dataSubSection = 'advanced';
        }

        Log::debug("paramsInUse: " . json_encode($paramsInUse));

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $emptyResponse = [
                'status' => 'success',
                'message' => 'Insights data empty',
                'data' => [
                    $dataSubSection => [
                        'lastMeasured' => '',
                        'profile' => [],
                    ]
                ]
        ];

        $request = VideoRequest::where('user_id', $userId)
            ->whereHas('videos')
            ->whereHas('emloInsightsParamAggregates')
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$request) {
            Log::info('No video request with aggregation found for user: ' . $userId);
            return response()->json($emptyResponse);
        }

        $credScoreValue = CredScoreValue::select('id')
            ->where("request_id", $request->id)
            ->first();
        if ($credScoreValue) {
            $request = $request->whereHas('credScoreInsightsAggregates')->first();
        }

        // Get the aggregation
        $aggregates = EmloInsightsParamAggregate::select(
                'emlo_param_spec_id',
                'since_start',
                "since_start_progress_over_time",
                'morning',
                'afternoon',
                'evening',
                'total_average',
                'last_30_days_progress_over_time')
            ->where('request_id', $request->id)
            ->get();
        if (!$aggregates) {
            Log::info('No EMLO insights aggregate found for user: ' . $userId . ', request: ' . $request->id);
            return response()->json($emptyResponse);
        }

        $latestValues = $this->getRawParamValues($request->id);
        Log::debug("requestid is {$request->id}");
        Log::debug("latestValues: " . json_encode($latestValues));

        $allEmotionsData = [];

        foreach ($paramsInUse as $paramInUse) {
            $aggregatesOfParam = $aggregates->where('emlo_param_spec_id', $paramInUse->id)->first();
            Log::debug("paramInUse is: " . json_encode($paramInUse));
            $latestValue = $latestValues->firstWhere('emlo_param_spec_id', $paramInUse->id)?->value ?? 0;
            $latestValue = (int) ($paramInUse->needs_normalization ? EmloHelperService::applyNormalizationFormula($latestValue) : $latestValue);

            $lastMeasured = $latestValues->firstWhere('emlo_param_spec_id', $paramInUse->id)?->created_at->format('M j, Y') ?? '';
            $lastMeasuredDetailed = $latestValues
                ->firstWhere('emlo_param_spec_id', $paramInUse->id)?->created_at
                ->format('M j, Y H:i') ?? '';


            if ($latestValue != 0) {
                $conditionMet = $this->ruleEvaluationService->quickRuleCheck($latestValue, $latestValues, $paramInUse);
            } else if ($latestValue == 0 && $paramInUse->param_name == 'Aggression') {
                $conditionMet = $this->ruleEvaluationService->quickRuleCheck($latestValue, $latestValues, $paramInUse);
            }
            
            $timeOfDayAverages = $this->createTimeofDayAverages($aggregatesOfParam);

            $weeklyData = $this->averagesService->createWeeklyData($aggregatesOfParam);

            $thirtyDayData = $this->averagesService->create30DayData($aggregatesOfParam);

            $threeMonthsData = $this->averagesService->aggregateMonthlyData($aggregatesOfParam, '3months');
            $sixMonthsData = $this->averagesService->aggregateMonthlyData($aggregatesOfParam, '6months');
            $monthsSinceStartData = $this->averagesService->aggregateMonthlyData($aggregatesOfParam, 'since_start');


            if ($dataSubSection == 'emotions') {
                $emotionData = $this->createEmotionData(
                    $paramInUse, 
                    $aggregatesOfParam,
                    $lastMeasuredDetailed,
                    $latestValue,
                    $conditionMet,
                        $weeklyData,
                        $timeOfDayAverages,
                            $thirtyDayData,
                        $threeMonthsData,
                        $sixMonthsData,
                        $monthsSinceStartData
                );
            } else if ($dataSubSection == 'advanced') {
                $emotionData = $this->createSecondaryMetricData(
                    $paramInUse, 
                    $aggregatesOfParam,
                    $latestValue,
                    $lastMeasuredDetailed,
                    $conditionMet,
                        $weeklyData,
                        $timeOfDayAverages,
                            $thirtyDayData,
                        $threeMonthsData,
                        $sixMonthsData,
                        $monthsSinceStartData
                );
            }
            
            $allEmotionsData [] = $emotionData;
            Log::debug("allEmotionsData: " . json_encode($allEmotionsData));
        }

        if ($dataSubSection == 'advanced') $allEmotionsData = $this->orderSecondaryMetricsFinalArray($allEmotionsData);
        if ($dataSubSection == 'emotions') $allEmotionsData = $this->orderEmotionsFinalArray($allEmotionsData);

        return response()->json([
                'status' => 'success',
                'message' => 'Insights data retrieved successfully',
                'data' => [
                    $dataSubSection => [
                        'lastMeasured' => $lastMeasured ?? '',
                        'profile' => $allEmotionsData ?? [],
                    ]
                ]
            ]
        );
    }

    private function createEmotionData(
        $paramInUse = null, 
        $aggregate = null, 
        $lastMeasured,
        $latest_value, 
        $conditionMet,
        $weeklyData,
        $timeOfDayAverages,
        $thirtyDayData,
        $threeMonthsData,
        $sixMonthsData,
        $monthsSinceStartData
        ) 
    {

        return [
            "id" => $paramInUse->simplified_param_name ?? '',
            "emoji"=> $paramInUse->emoji ?? '',
            "name"=> $paramInUse->simplified_param_name ?? '',
            "current" => $latest_value ?? 0,
            "average" => $aggregate->total_average ?? 0,
            "lastMeasured" => $lastMeasured ?? '',
            "range" => $conditionMet->emotion_performance ?? '',
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

    private function createSecondaryMetricData(
        $paramInUse = null, 
        $aggregate = null, 
        $latest_value,
        $lastMeasured,
        $conditionMet,
        $weeklyData,
        $timeOfDayAverages,
        $thirtyDayData,
        $threeMonthsData,
        $sixMonthsData,
        $monthsSinceStartData
        ) 
    {

        $statusType = '';
        if (!empty($conditionMet)) {
            $statusType = $this->getStatusType($paramInUse, $conditionMet->emotion_performance);
        }

        return [
            "id" => $paramInUse->simplified_param_name ?? '',
            "emoji"=> $paramInUse->emoji ?? '',
            "name"=> $this->changeParamName($paramInUse) ?? $paramInUse->simplified_param_name?? "",
            "current" => $latest_value ?? 0,
            "average" => $aggregate->total_average ?? 0,
            "lastMeasured" => $lastMeasured ?? '',
            "range" => $conditionMet->emotion_performance ?? '',
            "description" => $paramInUse->description ?? '',
            "status" => $conditionMet->emotion_performance ?? '',
            "statusMessage" => $conditionMet->message ?? '',
            "statusType" => $statusType,
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

    private function orderEmotionsFinalArray($emotions)
    {
        foreach ($emotions as $index => &$emotion) {
            $emotion['_originalIndex'] = $index;
        }
        unset($emotion);

        usort($emotions, function ($a, $b) {
            $dateA = strtotime($a['lastMeasured'] ?? '1970-01-01 00:00:00');
            $dateB = strtotime($b['lastMeasured'] ?? '1970-01-01 00:00:00');

            if ($dateA === $dateB) {
                return $a['_originalIndex'] <=> $b['_originalIndex'];
            }

            return $dateB <=> $dateA; // newest first
        });

        foreach ($emotions as &$emotion) {
            unset($emotion['_originalIndex']);
        }
        unset($emotion);

        return $emotions; // ✅ return sorted
    }


    private function orderSecondaryMetricsFinalArray($emotions)
    {

        Log::debug("emotions are: " . json_encode($emotions));
            // Define the order you want
            $desiredOrder = [
                'Self Honesty',
                'Stress Recovery',
                'Cognitive Balance',
                'Anger',
            ];

            // Rebuild array based on desired order
            $ordered = [];
            foreach ($desiredOrder as $emotionName) {
                foreach ($emotions as $emotion) {
                    if ($emotion['name'] === $emotionName) {
                        $ordered[] = $emotion;
                        break;
                    }
                }
            }

            // Replace profile with ordered version
            $emotions = $ordered;

        return $emotions;
    }



    private function getStatusType($paramInUse, $status)
    {
        switch ($paramInUse->param_name) {
            case 'self_honesty':
                if ($status == 'Normal') {
                    return 'Poor';
                } else if ($status == 'Above Normal') {
                    return 'Good';
                } else if ($status == 'High') {
                    return 'Great';
                } else {
                    return '';
                }

            case 'clStress':
                if ($status == 'No stress - emotionally disengaged'
                || $status == 'High stress with difficult recovery'
                || $status == 'High stress with no recovery'
                || $status == 'Extreme stress requiring attention') {
                    return 'Poor';
                } else if ($status == 'Medium stress with good recovery'
                || $status == 'High stress with good recovery') {
                    return 'Good';
                } else if ($status == 'Low stress with good recovery') {
                    return 'Great';
                } else {
                    return '';
                }

            case 'overallCognitiveActivity':
                if ($status == 'Disconnected'
                || $status == 'Tense'
                || $status == 'Overloaded ') {
                    return 'Poor';
                } else if ($status == 'Steady') {
                    return 'Great';
                } else {
                    return '';
                }

            case 'Aggression':
                if ($status == 'Above Normal' || $status == 'High') {
                    return 'Poor';
                } else if ($status == 'Normal') {
                    return 'Great';
                } else {
                    return '';
                }
            default:
                return '';
        }

    }

    private function changeParamName($paramInUse)
    {
        if ($paramInUse->param_name == 'Aggression') {
            return 'Anger';
        } else if ($paramInUse->param_name == 'self_honesty') {
            return 'Self Honesty';
        } else {
            return null;
        }
    }

    private function createTimeofDayAverages($aggregate = null) 
    {
        return [
            'morning' => (int)$aggregate->morning ?? '',
            'afternoon' => (int)$aggregate->afternoon ?? '',
            'evening' => (int)$aggregate->evening ?? '',
        ];
    }

    private function getRawParamValues($requestId)
    {
        $response = EmloResponse::select('id')
            ->where('request_id', $requestId)
            ->first();

        if(!$response) {
            throw new EmloNotFoundException("EMLO response not found for request {$requestId}");
        }

        Log::debug("response is: " . json_encode($response) . "request  is: " . json_encode($requestId));

        // Get regular param values
        $regularValues = EmloResponseValue::select('emlo_response_values.numeric_value', 'emlo_response_values.string_value', 'emlo_response_paths.emlo_param_spec_id', 'emlo_response_values.created_at')
                ->join('emlo_response_paths', 'emlo_response_values.path_id', '=', 'emlo_response_paths.id')
                ->whereNotNull('emlo_response_paths.emlo_param_spec_id')
                ->where('emlo_response_values.response_id', $response->id)
                ->get();

        // Get segment param values
        $segmentValues = EmloResponseValue::select('numeric_value', 'emlo_param_spec_id', 'emlo_response_values.created_at')
                ->where('emlo_response_values.response_id', $response->id)
                ->whereNotNull('emlo_param_spec_id')
                ->get();

        Log::debug("segment values are: " . json_encode($segmentValues));

        $regularParams = collect($regularValues->map(function ($responseValue) {
            return (object) [
                'emlo_param_spec_id' => $responseValue->emlo_param_spec_id ?? null,
                'value' => $responseValue->numeric_value ?? (int)($responseValue->string_value ?? 0),
                'created_at' => $responseValue->created_at ?? null
            ];
        }));

        // Transform segment param values
        $segmentParams = collect($segmentValues->map(function ($responseValue) {
            return (object) [
                'emlo_param_spec_id' => $responseValue->emlo_param_spec_id ?? null,
                'value' => $responseValue->numeric_value ?? (int)($responseValue->string_value ?? 0),
                'created_at' => $responseValue->created_at ?? null
            ];
        }));

        // Combine both collections
        $allParams = $regularParams->merge($segmentParams);

        return $allParams;
    }

    public function getInsightsData(Request $request, $paramName)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $filterBy = $request->get('filter_by', 'last_7_days');
        $allowedFilters = ['last_7_days', 'last_30_days', 'since_start'];
        if (!in_array($filterBy, $allowedFilters)) $filterBy = 'last_7_days';

        $activity = $this->getUserActivity($userId, $filterBy);

        $emptyResponse = [
            'status' => 'success',
            'message' => 'Insights data retrieved successfully',
            'data' => [
                'metricsData' => [],
                'timeOfDayData' => [],
                'secondaryMetrics' => [],
                'risk' => [],
                'activity' => $activity ?? [],
                'progressData' => [],
                'info' => [
                    'metric' => "Track how your emotional signals change throughout the week. Use the dropdown to explore different emotions and view daily trends, along with averages for the last 7 days, last 30 days, and since start.",
                    'timeOfDay' => 'See how your emotions shift throughout the day. Select an emotion to view patterns by morning, afternoon, and evening—along with averages for the week, month, and overall trends since you began.',
                    'progress' => '',
                    'stressRecovery' => "Shows how strongly anger or tension is coming through in your voice. It ranges from calm confidence to intense frustration—sometimes a sign to pause, reflect, or reset.",
                    'cognitiveBalance' => "Reflects how steady and aligned your thoughts are. Healthy balance means you’re thinking clearly and calmly. Disruptions may signal mental strain, overwhelm, or inner conflict.",
                    'aggression' => 'Shows how strongly anger or tension is coming through in your voice. It ranges from calm confidence to intense frustration—sometimes a sign to pause, reflect, or reset.',
                    'selfHonesty' => "Reveals a sense of openness and authenticity in your voice. It suggests you’re speaking with clarity and emotional honesty—comfortable with your thoughts and willing to be real, even in vulnerable moments.",
                    'activity' => '',
                    'streak' => ''
                ]
            ]
        ];

        // Find the latest VideoRequest for the user that has aggregation for the given paramName
        $emloParamSpecId = EmloResponseParamSpecs::select('id')
            ->where('param_name', $paramName)
            ->first();
        if (!$emloParamSpecId) {
            Log::info('No EMLO param spec found for user: ' . $userId . ', param: ' . $paramName);
            return response()->json($emptyResponse);
        }

        // Find the latest VideoRequest that has aggregation
        $requestId = VideoRequest::where('user_id', $userId)
            ->whereHas('videos')
            ->whereHas('emloInsightsParamAggregates', function($q) use ($emloParamSpecId) {
                $q->where('emlo_param_spec_id', $emloParamSpecId->id);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$requestId) {
            Log::info('No video request with aggregation found for user: ' . $userId);
            return response()->json($emptyResponse);
        }

        // Get the aggregation
        $aggregate = EmloInsightsParamAggregate::select(
                $filterBy,
                "{$filterBy}_progress_over_time",
                'morning',
                'afternoon',
                'evening')
            ->where('request_id', $requestId->id)
            ->where('emlo_param_spec_id', $emloParamSpecId->id)
            ->first();

        if (!$aggregate) {
            Log::info('No EMLO insights aggregate found for user: ' . $userId . ', request: ' . $requestId->id);
            return response()->json($emptyResponse);
        }

        $metricsData = [];
        $timeOfDayAverages = [
            'Morning' => $aggregate->morning,
            'Afternoon' => $aggregate->afternoon,
            'Evening' => $aggregate->evening,
        ];
        
        
        switch ($filterBy) {
            case 'last_7_days':
                $metricsData = json_decode($aggregate->last_7_days);
                $progressData = json_decode($aggregate->last_7_days_progress_over_time);
                break;
            case 'last_30_days':
                $metricsData = json_decode($aggregate->last_30_days);
                $progressData = json_decode($aggregate->last_30_days_progress_over_time);
                break;
            case 'since_start':
                $metricsData = json_decode($aggregate->since_start);
                $progressData = json_decode($aggregate->since_start_progress_over_time);
                break;
            default:
                $metricsData = json_decode($aggregate->last_7_days);
                $progressData = json_decode($aggregate->last_7_days_progress_over_time);
                break;
        }

        // Get all secondary metric arrays for the request
        $secondaryMetricArrays = EmloInsightsSecondaryMetric::select('info_array')
            ->where('request_id', $requestId->id)
            ->get();

        if ($secondaryMetricArrays->isEmpty()) {
            return response()->json($emptyResponse);
        }

        $desiredOrder = [
            'stressRecovery',
            'cognitiveBalance',
            'aggression'
        ];
        $selfHonesty = [];

        // Process and collect all metrics
        $allMetrics = [];
        foreach ($secondaryMetricArrays as $record) {
            $metricData = json_decode($record->info_array, true);
            if ($metricData && isset($metricData['name'])) {
                if ($metricData['name'] != 'selfHonesty') {
                    $allMetrics[$metricData['name']] = $metricData;
                } else {
                    $selfHonesty = $metricData;
                }
                
            }
        }

        // Build ordered array based on desired order
        $secondaryMetrics = [];
        foreach ($desiredOrder as $metricName) {
            if (isset($allMetrics[$metricName])) {
                $secondaryMetrics[] = $allMetrics[$metricName];
            }
        }

        // Optionally, add any remaining metrics not in the desired order
        foreach ($allMetrics as $metricName => $metricData) {
            if (!in_array($metricName, $desiredOrder)) {
                $secondaryMetrics[] = $metricData;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Insights data retrieved successfully',
            'data' => [
                'metricsData' => $metricsData ?? [],
                'timeOfDayData' => $timeOfDayAverages ?? [],
                'secondaryMetrics' => $secondaryMetrics ?? [],
                'risk' => $selfHonesty ?? [],
                'activity' => $activity ?? [],
                'progressData' => $progressData ?? [],
                'info' => [
                    'metric' => "Track how your emotional signals change throughout the week. Use the dropdown to explore different emotions and view daily trends, along with averages for the last 7 days, last 30 days, and since start.",
                    'timeOfDay' => 'See how your emotions shift throughout the day. Select an emotion to view patterns by morning, afternoon, and evening—along with averages for the week, month, and overall trends since you began.',
                    'progress' => '',
                    'stressRecovery' => "Shows how strongly anger or tension is coming through in your voice. It ranges from calm confidence to intense frustration—sometimes a sign to pause, reflect, or reset.",
                    'cognitiveBalance' => "Reflects how steady and aligned your thoughts are. Healthy balance means you’re thinking clearly and calmly. Disruptions may signal mental strain, overwhelm, or inner conflict.",
                    'aggression' => 'Shows how strongly anger or tension is coming through in your voice. It ranges from calm confidence to intense frustration—sometimes a sign to pause, reflect, or reset.',
                    'selfHonesty' => "Reveals a sense of openness and authenticity in your voice. It suggests you’re speaking with clarity and emotional honesty—comfortable with your thoughts and willing to be real, even in vulnerable moments.",
                    'activity' => '',
                    'streak' => ''
                ]
            ],
        ]);
    }

    public function getUserActivity($userId, $filterBy)
    {
        $stats = $this->insightsV2Service->getUserActivityStats($userId);
        $daysOfWeek = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
        $fullDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $weeklyActivity = array_fill(0, 7, 0);

        # Current week
        # $now = Carbon::now();
        # $startOfWeek = $now->copy()->startOfWeek(Carbon::SUNDAY);
        # $endOfWeek = $now->copy()->endOfWeek(Carbon::SATURDAY);

        $timeWindow = $this->getTimeWindow($filterBy, $userId);
        $startOfWeek = $timeWindow['start'];
        $endOfWeek = $timeWindow['end'];
        $videoRequests = VideoRequest::where('user_id', $userId)
            ->whereHas('videos')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get(['created_at']);

        $daysWithRecording = [];
        foreach ($videoRequests as $videoRequest) {
            $dayIndex = array_search(Carbon::parse($videoRequest->created_at)->format('l'), $fullDays);
            if ($dayIndex !== false) {
                $daysWithRecording[$dayIndex] = 1;
            }
        }
        foreach ($daysWithRecording as $i => $val) {
            $weeklyActivity[$i] = 1;
        }

        $weeklyActivityData = [];
        foreach ($daysOfWeek as $i => $day) {
            $weeklyActivityData[] = [
                'day' => $day,
                'active' => $weeklyActivity[$i],
            ];
        }

        return [
            'weekly' => $weeklyActivityData,
            'stats' => $stats
        ];
    }

    private function getTimeWindow($filterBy, $userId)
    {
        $now = Carbon::now();

        switch ($filterBy) {
            case 'last_7_days':
                return [
                    'start' => $now->copy()->subDays(7),
                    'end' => $now->copy()
                ];

            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(30),
                    'end' => $now->copy()
                ];

            case 'since_start':
                $query = DB::table('video_requests');
                
                // Add user filter if provided
                if ($userId) {
                    $query->where('user_id', $userId);
                }
                
                $earliest = $query->min('created_at');
                
                return [
                    'start' => $earliest ? Carbon::parse($earliest) : $now->copy()->subYear(),
                    'end' => $now->copy()
                ];

            default:
                return [
                    'start' => $now->copy()->subDays(7),
                    'end' => $now->copy()
                ];
        }
    }   
}
