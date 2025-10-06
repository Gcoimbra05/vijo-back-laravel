<?php

namespace App\Services\Emlo\EmloInsights;

use App\Exceptions\CredScore\CredScoreNotFoundException;
use App\Exceptions\Emlo\EmloNotFoundException;
use App\Exceptions\VideoRequestNotFoundException;

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
use Exception;
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
        protected RuleEvaluationService $ruleEvaluationService,
        protected EmloHelperService $emloHelperService){}

    public function getInsightsDataV2(Request $request)
    {
        $routeName = Route::currentRouteName();

        if ($routeName == 'api.v2.insights.v2.secondary-metrics') {
            $paramsInUse = EmloDatabaseLoader::getSecondaryMetricParams();
            $dataSubSection = 'advanced'; 
        } else {
            $paramsInUse = EmloDatabaseLoader::getEdpParamsInUse();
            $dataSubSection = 'emotions';
        }

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'user not found'], 404);
        }

        try{
            $request = VideoRequest::where('user_id', $userId)
                ->whereHas('videos')
                ->whereHas('emloInsightsParamAggregates')
                ->orderBy('created_at', 'desc')
                ->first();
            if (!$request) {
                Log::info('No video request with aggregation found for user: ' . $userId);
                throw New VideoRequestNotFoundException("video request w/ insights not found for user {$userId}");
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
                throw new EmloNotFoundException("emlo insights not found for request {$request->id}");
            }

            $latestValues = $this->getRawParamValues($request->id);

            $allEmotionsData = [];

            foreach ($paramsInUse as $paramInUse) {
                $aggregatesOfParam = $aggregates->where('emlo_param_spec_id', $paramInUse->id)->first();

                $latestValue = $latestValues->firstWhere('emlo_param_spec_id', $paramInUse->id)?->value ?? 0;
                $latestValue = (int) ($paramInUse->needs_normalization ? EmloHelperService::applyNormalizationFormula($latestValue, $paramInUse->param_name) : $latestValue);

                $lastMeasured = $latestValues->firstWhere('emlo_param_spec_id', $paramInUse->id)?->created_at->format('M j, Y') ?? '';
                $lastMeasuredDetailed = $latestValues
                    ->firstWhere('emlo_param_spec_id', $paramInUse->id)?->created_at
                    ->format('M j, Y g:iA') ?? '';


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
            } 
        } catch (Exception) {
            foreach ($paramsInUse as $paramInUse) {
                if ($dataSubSection == 'emotions') {
                    $emotionData = $this->createEmotionData($paramInUse);
                } else if ($dataSubSection == 'advanced') {
                    $emotionData = $this->createSecondaryMetricData($paramInUse);
                }
                $allEmotionsData [] = $emotionData;
            }
        }
        
        if ($dataSubSection == 'advanced') $allEmotionsData = $this->orderSecondaryMetricsFinalArray($allEmotionsData);
        if ($dataSubSection == 'emotions') $allEmotionsData = $this->emloHelperService->orderInsightsFinalArray($allEmotionsData);

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
        $lastMeasured = null,
        $latest_value = null, 
        $conditionMet = null,
        $weeklyData = null,
        $timeOfDayAverages = null,
        $thirtyDayData = null,
        $threeMonthsData = null,
        $sixMonthsData = null,
        $monthsSinceStartData = null
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
        $latest_value = null,
        $lastMeasured = null,
        $conditionMet = null,
        $weeklyData = null,
        $timeOfDayAverages = null,
        $thirtyDayData = null,
        $threeMonthsData = null,
        $sixMonthsData = null,
        $monthsSinceStartData = null
        ) 
    {

        $statusType = '';
        if (!empty($conditionMet)) {
            $statusType = $this->getStatusType($paramInUse, $conditionMet->emotion_performance_secondary);
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

    public function getRawParamValues($requestId)
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

    public function getUserActivity($userId, $filterBy)
    {
        $stats = $this->insightsV2Service->getUserActivityStats($userId);
        $daysOfWeek = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
        $fullDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $weeklyActivity = array_fill(0, 7, 0);

        $now = Carbon::now();
        $startDate = $now->copy()->startOfWeek(Carbon::SUNDAY);
        $endDate = $now->copy()->endOfWeek(Carbon::SATURDAY);

        $videoRequests = VideoRequest::where('user_id', $userId)
            ->whereHas('videos')
            ->whereBetween('created_at', [$startDate, $endDate])
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
            'stats' => $stats,
            'start_date' => $startDate->format('M d, Y'),
            'end_date' => $endDate->format('M d, Y')
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
