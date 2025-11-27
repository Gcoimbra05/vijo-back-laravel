<?php

namespace App\Services\Emlo\EmloInsights;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\EmloInsightsParamAggregate;
use App\Models\EmloResponseParamSpecs;
use App\Models\User;
use App\Models\VideoRequest;
use App\Services\CredScore\CredScoreService;
use App\Services\Emlo\EmloDatabaseLoader;
use App\Services\Emlo\EmloInsights\AveragesService;
use App\Services\Emlo\EmloInsights\InsightsV2Service;
use App\Services\Emlo\EmloInsights\ProgressOverTimeService;
use App\Services\Rules\RuleEvaluationService;
use App\Services\Emlo\EmloResponseService;
use App\Services\Emlo\EmloHelperService;

class EmloInsightsService
{
    public function __construct(
        protected EmloResponseService $emloResponseService,
        protected ProgressOverTimeService $progressOverTimeService,
        protected InsightsV2Service $insightsV2Service,
        protected AveragesService $averagesService,
        protected CredScoreService $credScoreService,
        protected RuleEvaluationService $ruleEvaluationService,
        protected EmloHelperService $emloHelperService){}

    public function getInsightsDataForSingleParam($userId, $requestId, $paramName)
    {
        $paramSpec = EmloResponseParamSpecs::select('*')->where('param_name', $paramName)
            ->first();
        if (!$paramSpec) {
            return [];
        }

        $aggregates = EmloInsightsParamAggregate::select('*')
            ->where('request_id', $requestId)
            ->where('emlo_param_spec_id', $paramSpec->id)
            ->first();
        if (!$aggregates) {
            return $this->createEmotionDataArray($paramSpec);
        }

        /** @var \Illuminate\Support\Collection $allValuesOfParam */
        $allValuesOfParam = $this->emloResponseService->getAllRawParamValues($userId, false, $paramSpec->id);
        if (empty($allValuesOfParam)) {
            return $this->createEmotionDataArray($paramSpec);
        }

        foreach ($allValuesOfParam as $valueOfParam) {
            if ($valueOfParam->string_value == null && $valueOfParam->numeric_value != null) {
                $valueOfParam->value = $valueOfParam->numeric_value;
            } else if ($valueOfParam->string_value != null && $valueOfParam->numeric_value == null) {
                $valueOfParam->value = $valueOfParam->string_value;
            } else {
                $valueOfParam->value = null;
            }

            if ($paramSpec->needs_normalization) {
                $valueOfParam->value = (int) EmloHelperService::applyNormalizationFormula($valueOfParam->value, $paramSpec->param_name);
            }
        }
        
        $latestValue = $allValuesOfParam?->sortByDesc('created_at')->first()->value;  
        $createdAt = $allValuesOfParam?->sortByDesc('created_at')->first()?->created_at;
        $conditionMet = $this->ruleEvaluationService->quickRuleCheck($latestValue, $allValuesOfParam, $paramSpec);
        
        $emotionData = $this->createEmotionDataArray(
            $paramSpec, 
            $createdAt,
            $latestValue,
            $conditionMet,
            $aggregates
        );
        return $emotionData;
    }

    public function getInsightsData($routeName, $userId, $baseline1, $baseline2)
    {
        Log::debug("baseline1: " . json_encode($baseline1) . "baseline2: " . json_encode($baseline2));

        $paramsInUse = [];
        $dataSubSection = '';
        //$filterId = $request->get('filter_id');
        //$compareId = $request->get('compare_id');

        if ($routeName == 'api.v2.insights.v2.secondary-metrics') {
            $paramsInUse = EmloDatabaseLoader::getSecondaryMetricParams();
            $dataSubSection = 'advanced'; 
        } else {
            $paramsInUse = EmloDatabaseLoader::getEdpParamsInUse();
            $dataSubSection = 'emotions';
        }

        $allEmotionsData = $this->constructDataArrayForBaselines($userId, $paramsInUse, $dataSubSection, $routeName, $baseline1, $baseline2);

        $data = [
            $dataSubSection => [
                'lastMeasured' => $lastMeasured ?? '',
                'profile' => $allEmotionsData ?? [],
            ]
        ];
        return $data;
    }

    private function constructDataArrayForBaselines($userId, $paramsInUse, $dataSubSection, $routeName, $baseline1, $baseline2)
    {
        if ($baseline1 && !$baseline2) {
            Log::debug("1st branch");
            $allEmotionsData = $this->retrieveDataAndFillUpDataArray($userId, $paramsInUse, $dataSubSection, $routeName, $baseline1);
        } else if ($baseline1 && $baseline2) {
            Log::debug("2nd branch");
            $baseline2Data = $this->retrieveDataAndFillUpDataArray($userId, $paramsInUse, $dataSubSection, $routeName, $baseline2);
            Log::debug("baseline2 data: " . json_encode($baseline2Data));
            $allEmotionsData = $this->retrieveDataAndFillUpDataArray($userId, $paramsInUse, $dataSubSection, $routeName, $baseline1, $baseline2Data);
            Log::debug("data about to be returned: " . json_encode($allEmotionsData));
        } else {
            Log::debug("3rd branch");
            $allEmotionsData = $this->retrieveDataAndFillUpDataArray($userId, $paramsInUse, $dataSubSection, $routeName);
        }

        if(!$allEmotionsData) {
            return $this->buildEmptyResponseArray($paramsInUse, $dataSubSection);
        }
        return $allEmotionsData;
    }
    private function retrieveDataAndFillUpDataArray($userId, $paramsInUse, $dataSubSection, $routeName, $baseline = null, $compareArray = null)
    {
        $request = VideoRequest::latestWithVideoAndParamAggregates($userId, $baseline?->starts_at, $baseline?->ends_at);
        if (!$request) {
            return [];
        }
        Log::debug("the request is: " . json_encode($request));

        $aggregates = EmloInsightsParamAggregate::select('*')
            ->where('request_id', $request->id)
            ->get();
        // Check if the collection is empty
        if ($aggregates->isEmpty()) {
            return [];
        }

        Log::debug("the aggregates are: " . json_encode($aggregates));

        $allValuesOfAllParams = $this->resolveAllParamValues($routeName, $userId, $baseline?->starts_at, $baseline?->ends_at);
        if ($allValuesOfAllParams->isEmpty()) {
            return [];
        }

        Log::debug("the allValuesOfAllParams" . "for baseline: " . json_encode($baseline) . "are: " . json_encode($allValuesOfAllParams));

        $allEmotionsData = [];

        foreach ($paramsInUse as $paramInUse) {
            $aggregatesOfParam = $aggregates
                ->where('emlo_param_spec_id', $paramInUse->id)
                ->sortByDesc('created_at')
                ->first();

            /** @var \Illuminate\Support\Collection $allValuesOfParam */
            $allValuesOfParam = $allValuesOfAllParams->get($paramInUse->id);
            if (!$allValuesOfParam) {
                $allEmotionsData [] = $this->createEmotionDataArray($paramInUse);
                continue;
            }

            $this->normalizeAllParamValues($paramInUse, $allValuesOfParam);

            $latestValue = $allValuesOfParam?->sortByDesc('created_at')->first()->value;
            $createdAt = $allValuesOfParam?->sortByDesc('created_at')->first()?->created_at;
            $conditionMet = $this->ruleEvaluationService->quickRuleCheck($latestValue, $allValuesOfParam, $paramInUse);
            
            $compareArrayOfParam = null;
            if ($compareArray) {
                $searchId = $paramInUse->simplified_param_name;
                $filtered = array_filter($compareArray, function($item) use ($searchId) {
                    return isset($item['id']) && $item['id'] === $searchId;
                });
                $compareArrayOfParam = !empty($filtered) ? reset($filtered) : null;
            }
            
            $emotionData = $this->createDataArrayForSubsection($dataSubSection, $paramInUse, $createdAt, $latestValue, $conditionMet, $aggregatesOfParam, $compareArrayOfParam);
            $allEmotionsData [] = $emotionData;
        } 
        
        if ($dataSubSection == 'advanced') $allEmotionsData = $this->orderSecondaryMetricsFinalArray($allEmotionsData);
        if ($dataSubSection == 'emotions') $allEmotionsData = $this->emloHelperService->orderInsightsFinalArray($allEmotionsData);

        Log::debug("data about to be returned:" . json_encode($allEmotionsData));
        return $allEmotionsData;
    }

    private function createDataArrayForSubsection($dataSubSection, $paramInUse, $createdAt, $latestValue, $conditionMet, $aggregatesOfParam, $compareArray = null)
    {
        if ($dataSubSection == 'emotions') {
            $emotionData = $this->createEmotionDataArray(
                $paramInUse, 
                $createdAt,
                $latestValue,
                $conditionMet,
                $aggregatesOfParam,
                $compareArray

            );
        } else if ($dataSubSection == 'advanced') {
            $emotionData = $this->createSecondaryMetricDataArray(
                $paramInUse, 
                $latestValue,
                $createdAt,
                $conditionMet,
                $aggregatesOfParam,
                $compareArray
            );
        }
        return $emotionData;
    }

    private function resolveAllParamValues($routeName, $userId, $startsAt = null,  $endsAt = null)
    {
        if ($routeName == 'api.v2.insights.v2.secondary-metrics') {
            $allValuesOfAllParams = $this->emloResponseService->getAllRawParamValues($userId, true, null, $startsAt, endDate: $endsAt);
        } else {
            $allValuesOfAllParams = $this->emloResponseService->getAllRawParamValues($userId, null, null, $startsAt, endDate: $endsAt);
        }

        if ($allValuesOfAllParams->isEmpty()) {
            return collect(); // or new Collection()
        }
        
        foreach ($allValuesOfAllParams as $allValuesOfParam) {
            foreach ($allValuesOfParam as $valueOfParam) {
                if ($valueOfParam->string_value === null && $valueOfParam->numeric_value !== null) {
                    $valueOfParam->value = $valueOfParam->numeric_value;
                } else if ($valueOfParam->string_value !== null && $valueOfParam->numeric_value === null) {
                    $valueOfParam->value = $valueOfParam->string_value;
                } else {
                    $valueOfParam->value = null;
                }
            }
        }
        return $allValuesOfAllParams;
    }

    private function normalizeAllParamValues($paramInUse, $allValuesOfParam)
    {
        if ($paramInUse->needs_normalization) {
            foreach ($allValuesOfParam as $valueOfParam) {
                $valueOfParam->value = (int) EmloHelperService::applyNormalizationFormula($valueOfParam->value, $paramInUse->param_name);
            }
        }
    }

    private function buildEmptyResponseArray($paramsInUse, $dataSubSection)
    {
        $allEmotionsData = [];
        
        foreach ($paramsInUse as $paramInUse) {
            if ($dataSubSection == 'emotions') {
                $allEmotionsData[] = $this->createEmotionDataArray($paramInUse);
            } else if ($dataSubSection == 'advanced') {
                $allEmotionsData[] = $this->createSecondaryMetricDataArray($paramInUse);
            }
        }
        
        if ($dataSubSection == 'advanced') {
            $allEmotionsData = $this->orderSecondaryMetricsFinalArray($allEmotionsData);
        } else {
            $allEmotionsData = $this->emloHelperService->orderInsightsFinalArray($allEmotionsData);
        }

        return [
            $dataSubSection => [
                'lastMeasured' => '',
                'profile' => $allEmotionsData,
            ]
        ];
    }

    public function createEmotionDataArray(
        $paramInUse = null,
        $createdAt = null,
        $latest_value = null, 
        $conditionMet = null,
        $aggregatesOfParam = null,
        $compareArray = null
    ) 
    {
        $safeAggregate = $aggregatesOfParam ?? new \stdClass();
        
        return [
            "id" => $paramInUse?->simplified_param_name ?? '',
            "emoji" => $paramInUse?->emoji ?? '',
            "name" => $paramInUse?->simplified_param_name ?? '',
            "current" => $latest_value ?? 0,
            "average" => $safeAggregate->total_average ?? 0,
            "compare" => $compareArray ?? null,
            "lastMeasured" => $createdAt ? (Carbon::parse($createdAt)->format('M j, Y g:iA') ?? '') : '',
            "range" => $conditionMet?->emotion_performance ?? '',
            "dayChartData" => $aggregatesOfParam ? ($this->averagesService->createWeeklyData($aggregatesOfParam) ?? []) : [],
            "timeChartData" => $aggregatesOfParam ? ($this->createTimeofDayAverages($aggregatesOfParam) ?? []) : [],
            "timelineData" => [
                "30days" => $aggregatesOfParam ? ($this->averagesService->create30DayData($aggregatesOfParam) ?? []) : [],
                "3months" => $aggregatesOfParam ? ($this->averagesService->aggregateMonthlyData($aggregatesOfParam, '3months') ?? []) : [],
                "6months" => $aggregatesOfParam ? ($this->averagesService->aggregateMonthlyData($aggregatesOfParam, '6months') ?? []) : [],
                "all" => $aggregatesOfParam ? ($this->averagesService->aggregateMonthlyData($aggregatesOfParam, 'since_start') ?? []) : []
            ]
        ];
    }

    private function createSecondaryMetricDataArray(
        $paramInUse = null, 
        $latest_value = null,
        $createdAt = null,
        $conditionMet = null,
        $aggregatesOfParam = null,
        $compareArray = null
    ) 
    {
        $statusType = '';
        if (!empty($conditionMet)) {
            $statusType = $this->getStatusType($paramInUse, $conditionMet->emotion_performance_secondary);
        }
        
        return [
            "id" => $paramInUse?->simplified_param_name ?? '',
            "emoji" => $paramInUse?->emoji ?? '',
            "name" => $this->changeParamName($paramInUse) ?? $paramInUse?->simplified_param_name ?? "",
            "current" => $latest_value ?? 0,
            "average" => $aggregatesOfParam?->total_average ?? 0,
            "compare" => $compareArray ?? null,
            "lastMeasured" => $createdAt ? (Carbon::parse($createdAt)->format('M j, Y g:iA') ?? '') : '',
            "range" => $conditionMet?->emotion_performance ?? '',
            "description" => $paramInUse?->description ?? '',
            "status" => $conditionMet?->emotion_performance ?? '',
            "statusMessage" => $conditionMet?->message ?? '',
            "statusType" => $statusType,
            "dayChartData" => $aggregatesOfParam ? ($this->averagesService->createWeeklyData($aggregatesOfParam) ?? []) : [],
            "timeChartData" => $aggregatesOfParam ? ($this->createTimeofDayAverages($aggregatesOfParam) ?? []) : [],
            "timelineData" => [
                "30days" => $aggregatesOfParam ? ($this->averagesService->create30DayData($aggregatesOfParam) ?? []) : [],
                "3months" => $aggregatesOfParam ? ($this->averagesService->aggregateMonthlyData($aggregatesOfParam, '3months') ?? []) : [],
                "6months" => $aggregatesOfParam ? ($this->averagesService->aggregateMonthlyData($aggregatesOfParam, '6months') ?? []) : [],
                "all" => $aggregatesOfParam ? ($this->averagesService->aggregateMonthlyData($aggregatesOfParam, 'since_start') ?? []) : []
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

    public function getUserActivity($userId, $filterBy)
    {
        $stats = $this->insightsV2Service->getUserActivityStats($userId);
        $daysOfWeek = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
        $fullDays = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $weeklyActivity = array_fill(0, 7, 0);

        $user = User::find($userId);
        $timezone = $user && $user->timezone ? $user->timezone : config('app.timezone', 'America/New_York');

        $now = Carbon::now($timezone);
        $startDate = $now->copy()->startOfWeek(Carbon::SUNDAY);
        $endDate = $now->copy()->endOfWeek(Carbon::SATURDAY);

        $videoRequests = VideoRequest::where('user_id', $userId)
            ->whereHas('videos')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['created_at']);

        $daysWithRecording = [];
        foreach ($videoRequests as $videoRequest) {
            $dayIndex = array_search(Carbon::parse($videoRequest->created_at)->setTimezone($timezone)->format('l'), $fullDays);
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

    public function getCondensedUserActivity($userId)
    {
        $stats = $this->insightsV2Service->getUserActivityStats($userId);
        return $stats;
    }

    private function getTimestampsOfBaselines($baseline1, $baseline2)
    {

    }
}
