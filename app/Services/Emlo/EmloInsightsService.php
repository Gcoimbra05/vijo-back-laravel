<?php

namespace App\Services\Emlo;

use App\Exceptions\UserNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\Emlo\EmloResponseService;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UserLogin;
use App\Models\VideoRequest;
use App\Models\EmloResponse;
use App\Models\EmloResponseValue;


class EmloInsightsService
{
    public function __construct(protected EmloResponseService $emloResponseService){}

    public function getInsightsData(Request $request, $userId, $paramName)
    {        
        $filterBy = $request->get('filter_by', 'weekly');
        Log::debug('filter by is: ' . json_encode($filterBy));
        $timeWindow = $this->getTimeWindow($filterBy);
        $queryFilters = [
            "start_time" => $timeWindow['start'],  
            "end_time" => $timeWindow['end']
        ];

        // Get filtered data for the main result (current period only)
        $result = $this->emloResponseService->getAllValuesOfParam($paramName, $userId, $queryFilters);

        switch($filterBy) {
            case 'weekly':
                // Get ALL historical data for day averages (not just this week)
                $allHistoricalData = $this->emloResponseService->getAllValuesOfParam($paramName, $userId, []);
                $average = $this->createPerDayAverages($allHistoricalData);
                break;
            case 'monthly':
                // For monthly, you might also want all-time data for comparison
                $allHistoricalData = $this->emloResponseService->getAllValuesOfParam($paramName, $userId, []);
                $average = $this->createMonthlyAverage($allHistoricalData);
                break;
            case 'all_time':
                // For all_time, use the same dataset
                $average = $this->createAllTimeAverage($result);
                break;
        }

        $timeOfDayAverages = $this->createTimeOfDayAverages($allHistoricalData);

        $aggregatedData = $this->aggregateData($result, $filterBy, $average);

        $secondaryMetrics = $this->getSecondaryMetrics($userId);

        $selfHonesty = $this->getSelfHonestyInfo($userId);

        $daysOfWeek = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        $weeklyActivity = [];
        foreach ($daysOfWeek as $index => $day) {
            $weeklyActivity[] = [
                'day' => $day,
                'active' => rand(50, 100),
            ];
        }

        $userId = Auth::id();
        $stats = $this->getUserActivityStats($userId);

        $activity = [
            'weekly' => $weeklyActivity,
            'stats' => $stats
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Insights data retrieved successfully',
            'data' => [
                'metricsData' => $aggregatedData,
                'timeOfDayData' => $timeOfDayAverages,
                'secondaryMetrics' => $secondaryMetrics,
                'risk' => $selfHonesty,
                'activity' => $activity,
                'progressData' => $progressData ?? [],
            ],
        ]);
    }

    private function getSecondaryMetrics($userId)
    {
        $clStress = $this->emloResponseService->getAllValuesOfParam('clStress', $userId, []);
        $clStressPercentages = $this->sortClStressByValue($clStress);
        $clStressInfoArray = $this->createClStressInfoArray($clStressPercentages);

        $oCA = $this->emloResponseService->getAllValuesOfParam('overallCognitiveActivity', $userId, []);
        foreach ($oCA as $ocaValue) {
            $normalizedValue = EmloHelperService::applyNormalizationFormula($ocaValue->value);
            $ocaValue->value = $normalizedValue;
        }
        $oCAPercentages = $this->sortOCAByValue($oCA);
        $ocaInfoArray = $this->createOCAInfoArray($oCAPercentages);


        $aggression = $this->emloResponseService->getAllValuesOfParam('Aggression', $userId, []);
        $aggressionPercentages = $this->sortAggressionByValue($aggression);
        $aggressionInfoArray = $this->createAggressionInfoArray($aggressionPercentages);

        $returnArray = [
            $clStressInfoArray,
            $ocaInfoArray,
            $aggressionInfoArray
        ];

        return $returnArray;
    }

    private function getSelfHonestyInfo($userId)
    {
        $result = EmloResponseValue::select('response_id', 'path_id', 'numeric_value', 'string_value', 'boolean_value', 'created_at')
            ->where('path_id', 15)
            ->whereHas('response.request', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId);
            })
            ->first();

        $response = EmloResponse::select('raw_response')
            ->where('id', $result->response_id)
            ->first();
        
        $selfHonesty = $this->emloResponseService->handleSelfHonesty('self_honesty', $response);

        $description = '';
        if ($selfHonesty == 0) {
            $description = 'failed to fetch self honesty score';
        } else if ($selfHonesty >= 1 && $selfHonesty < 50) {
            $description = 'self honesty is low';
        } else if ($selfHonesty >= 50 && $selfHonesty < 80) {
            $description = 'self honesty is good';
        } else if ($selfHonesty >= 80 && $selfHonesty <= 100) {
            $description = 'self honesty is excellent';
        }

        $returnArray = [
            "name" => "selfHonesty",
            "title"=> "Self-Honesty",
            "description"=> "How honest you're being with yourself (80 - 100 is best)",
            "min" => 1,
            "midpoint"=> 50,
            "max"=> 100,
            "currentValue"=>  $selfHonesty,
            "stats" => [
                [
                    "label"=> "Current Level",
                    "value"=> $selfHonesty,
                    "description" => $description
                ],
                [
                    "label" => "Best Range",
                    "value" => "60-100",
                    "description" => "Honest self-reflection"
                ]
            ]
        ];

        return $returnArray;
    }

    private function sortClStressByValue($clStressValues)
    {
        $sortedValues = [
            "1" => 0,
            "2" => 0,
            "3" => 0,
            "4" => 0,
            "5" => 0,
        ];

        $newestValue = 0;


        foreach($clStressValues as $index => $clStressValue) {
            if ($clStressValue != null && isset($clStressValue->value)) {
                if ($index == 0) $newestValue = $clStressValue->value;

                switch ($clStressValue->value) {
                    case 1:
                        $sortedValues['1']++;
                        break;
                    case 2:
                        $sortedValues['2']++;
                        break;
                    case 3:
                        $sortedValues['3']++;
                        break;
                    case 4:
                        $sortedValues['4']++;
                        break;
                    case 5:
                        $sortedValues['5']++;
                        break;
                }
            }
        }

        $percentages = $this->getSecondaryMetricPercentages($sortedValues);

        $returnArray = [
            "percentages" => $percentages,
            "newestValue" => $newestValue
        ];
                
        return $returnArray;
    }

    private function sortOCAByValue($oCAValues)
    {
        $sortedValues = [
            "1" => 0,
            "2" => 0,
            "3" => 0,
            "4" => 0,
        ];

        $newestValue = 0;


        foreach($oCAValues as $index => $oCAValue) {
            if ($oCAValue != null && isset($oCAValue->value)) {
                if ($index == 0) $newestValue = $oCAValue->value;

                if (($oCAValue->value >= 0.05) && ($oCAValue->value < 5)) {
                    $sortedValues['1']++;
                } else if (($oCAValue->value >= 5) && ($oCAValue->value < 10)) {
                    $sortedValues['2']++;
                } else if (($oCAValue->value >= 10) && ($oCAValue->value < 15)) {
                    $sortedValues['3']++;
                } else if (($oCAValue->value >= 15) && ($oCAValue->value <= 17.5)) {
                    $sortedValues['4']++;
                }
            }
        }

        $percentages = $this->getSecondaryMetricPercentages($sortedValues);

        $returnArray = [
            "percentages" => $percentages,
            "newestValue" => $newestValue
        ];
                
        return $returnArray;
    }

    private function sortAggressionByValue($aggressionValues)
    {
        $sortedValues = [
            "1" => 0,
            "2" => 0,
            "3" => 0,
        ];

        $newestValue = 0;


        foreach($aggressionValues as $index => $aggressionValue) {
            if ($aggressionValue != null && isset($aggressionValue->value)) {
                if ($index == 0) $newestValue = $aggressionValue->value;

                if ($aggressionValue->value == 0) {
                    $sortedValues['1']++;
                } else if (($aggressionValue->value >= 1) && ($aggressionValue->value < 2)) {
                    $sortedValues['2']++;
                } else if ($aggressionValue->value > 2) {
                    $sortedValues['3']++;
                }
            }   
        }

        $percentages = $this->getSecondaryMetricPercentages($sortedValues);

        $returnArray = [
            "percentages" => $percentages,
            "newestValue" => $newestValue
        ];
            
        return $returnArray;
    }

    private function createClStressInfoArray($sortedClStressValues)
    {
        $clStressInfo = [
                "name" => "stressRecovery",
                "title"=> "Stress Recovery",
                "description"=> "Ability to return to calm after stress (Level 1 is best)",
                "currentValue"=> $sortedClStressValues['newestValue'],
                "items"=> [
                    [
                        "range"=> 1,
                        "label"=> "Excellent Recovery",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> 2,
                        "label"=> "Very Good",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> 3,
                        "label"=> "Good",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> 4,
                        "label"=> "Moderate",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> 5,
                        "label"=> "Needs Attention",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ]
                ]
            ];

        foreach ($clStressInfo['items'] as $index => &$item) {
            $item['percentage'] = $sortedClStressValues['percentages'][$index];
            if ($sortedClStressValues['newestValue'] == $item['range']) $item['isCurrent'] = true; 
        }
        return $clStressInfo;
    }

    private function createOCAInfoArray($sortedOCAValues)
    {
        $rangesForCalculation = [
            [0, 18],
            [18, 55],
            [55, 60],
            [60, 100]
        ];

        $ocaInfo = [
                "name"=> "cognitiveBalance",
                "title"=> "Cognitive Balance",
                "description"=> "How well thoughts and emotions work together",
                "currentValue"=> $sortedOCAValues['newestValue'],
                "items"=> [
                    [
                        "range"=> '0 - 18',
                        "label"=> "Disconnected",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> '18 - 55',
                        "label"=> "Low Balance",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> '55 - 60',
                        "label"=> "Balanced",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> '60 - 100',
                        "label"=> "Overstimulated",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ]
                ]
            ];

        foreach ($ocaInfo['items'] as $index => &$item) {
            $item['percentage'] = $sortedOCAValues['percentages'][$index];
            if ($sortedOCAValues['newestValue'] >= $rangesForCalculation[$index][0] && $sortedOCAValues['newestValue'] < $rangesForCalculation[$index][1]) $item['isCurrent'] = true; 
        }
        return $ocaInfo;
    }

    private function createAggressionInfoArray($sortedAggressionValues)
    {
        $rangesForCalculation = [
            [0, 0.99],
            [1, 1.99],
            [2, 100],
        ];

        $aggressionInfo = 
                [
                "name"=> "aggression",
                "title"=> "Aggression",
                "description"=> "How strongly anger comes through (0 is best)",
                "currentValue"=> $sortedAggressionValues['newestValue'],
                "items"=> [
                    [
                        "range"=> "0",
                        "label"=> "Best - No Aggression",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> "1 - 2",
                        "label"=> "Acceptable",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ],
                    [
                        "range"=> ">2",
                        "label"=> "Needs Attention",
                        "percentage"=> 0,
                        "isCurrent"=> false
                    ]
                ]
            ];


        foreach ($aggressionInfo['items'] as $index => &$item) {
            $item['percentage'] = $sortedAggressionValues['percentages'][$index];
            if ($sortedAggressionValues['newestValue'] >= $rangesForCalculation[$index][0] && $sortedAggressionValues['newestValue'] < $rangesForCalculation[$index][1]) $item['isCurrent'] = true; 
        }
        return $aggressionInfo;
    }

    private function getSecondaryMetricPercentages($sortedValues)
    {
        $sortedValues = array_values($sortedValues); // Re-index from 0
        $total = array_sum($sortedValues);
        
        if ($total == 0) {
            return $sortedValues;
        }
        
        $percentages = [];
        foreach ($sortedValues as $key => $count) {
            $percentages[$key] = round(($count / $total) * 100, 2);
        }
        
        return $percentages;
    }

    private function createPerDayAverages($allValuesOfParam)
    {
        try {
            $daysWValues = [
                'Monday' => [],
                'Tuesday' => [],
                'Wednesday' => [],
                'Thursday' => [],
                'Friday' => [],
                'Saturday' => [],
                'Sunday' => [],
            ];

            foreach ($allValuesOfParam as $valueOfParam) {
                $createdAt = Carbon::parse($valueOfParam->created_at);
                $dayName = $createdAt->format('l');
                
                // Add to the array (not overwrite)
                $daysWValues[$dayName][] = $valueOfParam->value;
            }

            // Calculate averages
            foreach($daysWValues as $day => $values) {
                Log::debug("$day values: " . json_encode($values));
                if (count($values) > 0) {
                    $daysWValues[$day] = array_sum($values) / count($values);
                } else {
                    $daysWValues[$day] = 0;
                }
            }

            Log::debug('Final averages: ' . json_encode($daysWValues));
            
            return $daysWValues;
            
        } catch (Exception $e) {
            Log::debug($e->getMessage());
        }
    }

    private function createMonthlyAverage($allValuesOfParam)
    {
        try {
            // Default to current month if not specified
            $targetMonth = now();
            
            $monthlyValues = [];
            
            foreach ($allValuesOfParam as $valueOfParam) {
                $createdAt = Carbon::parse($valueOfParam->created_at);
                
                // Check if this value belongs to the same month/year
                if ($createdAt->isSameMonth($targetMonth)) {
                    $monthlyValues[] = $valueOfParam->value;
                }
            }
            
            if (count($monthlyValues) > 0) {
                return array_sum($monthlyValues) / count($monthlyValues);
            }
            
            return 0;
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return 0;
        }
    }

    private function createAllTimeAverage($allValuesOfParam)
    {
        try {
            if (count($allValuesOfParam) > 0) {
                $total = 0;
                foreach ($allValuesOfParam as $valueOfParam) {
                    $total += $valueOfParam->value;
                }
                return $total / count($allValuesOfParam);
            }
            
            return 0;
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return 0;
        }
    }

    private function createTimeOfDayAverages($allValuesOfParam)
    {
        try {
            $timeOfDayParamValues = [
                'Morning' => [],
                'Afternoon' => [],
                'Evening' => []
            ];

            foreach ($allValuesOfParam as $valueOfParam) {
                $createdAt = Carbon::parse($valueOfParam->created_at);
                $hour = $createdAt->hour;

                // Use if/elseif instead of switch for range comparisons
                if ($hour >= 0 && $hour < 10) {
                    $timeOfDayParamValues['Morning'][] = $valueOfParam->value;
                } elseif ($hour >= 10 && $hour < 17) {
                    $timeOfDayParamValues['Afternoon'][] = $valueOfParam->value;
                } else { // 18-23
                    $timeOfDayParamValues['Evening'][] = $valueOfParam->value;
                }
            }

           foreach($timeOfDayParamValues as $timeOfDay => $values) {
                if (count($values) > 0) {
                    $timeOfDayParamValues[$timeOfDay] = (int) round(array_sum($values) / count($values));
                } else {
                    $timeOfDayParamValues[$timeOfDay] = 0; // or null, or whatever default you want
                }
            }

            return $timeOfDayParamValues;
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return [];
        }
    }



    private function getTimeWindow($filterBy)
    {
        $now = Carbon::now();

        switch ($filterBy) {
            case 'weekly':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];

            case 'monthly':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];

            case 'all_time':
                $earliest = DB::table('requests')->min('created_at');
                return [
                    'start' => $earliest ? Carbon::parse($earliest) : $now->copy()->subYear(),
                    'end' => $now->copy()
                ];

            default:
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
        }
    }

    private function aggregateData($result, $filterBy, $average)
    {
        // Handle both Collection and array inputs
        $collection = is_array($result) ? collect($result) : $result;

        // For weekly view, ensure all days are represented
        if ($filterBy === 'weekly') {
            return $this->aggregateWeeklyData($collection, $average);
        }

        if ($collection->isEmpty()) {
            return [];
        }

        // Group the data based on aggregation type
        $grouped = $collection->groupBy(function ($item) use ($filterBy) {
            $date = Carbon::parse($item->created_at);

            switch ($filterBy) {
                case 'monthly':
                    return $date->format('Y') . '-W' . $date->format('W');

                case 'all_time':
                    return $date->format('Y-m');

                default:
                    return $date->format('l');
            }
        });

        // Calculate aggregated metrics for each group
        $aggregatedResult = $grouped->map(function ($group, $period) use ($filterBy, $average) {
            // Extract values from the 'value' property
            $values = $group->pluck('value')->filter(function ($value) {
                return $value !== null && is_numeric($value);
            });

            // Handle case where no valid values exist
            if ($values->isEmpty()) {
                $periodAverage = $this->getPeriodAverage($period, $filterBy, $average);
                return [
                    'category' => $this->formatPeriodForDisplay($period, $filterBy),
                    'value' => 0,
                    'avg' => $periodAverage
                ];
            }

            // Get the appropriate average for this period
            $periodAverage = $this->getPeriodAverage($period, $filterBy, $average);

            return [
                'category' => $this->formatPeriodForDisplay($period, $filterBy),
                'value' => round($values->avg(), 2),
                'avg' => $periodAverage
            ];
        });

        // Sort the results properly and return as array
        return $aggregatedResult->sortBy('sort_order')->values()->all();
    }

    private function aggregateWeeklyData($collection, $average)
    {
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $result = [];

        // Group existing data by day
        $grouped = $collection->groupBy(function ($item) {
            $date = Carbon::parse($item->created_at);
            return $date->format('l');
        });

        // Create entry for each day of the week
        foreach ($daysOfWeek as $day) {
            if (isset($grouped[$day])) {
                // Day has data
                $values = $grouped[$day]->pluck('value')->filter(function ($value) {
                    return $value !== null && is_numeric($value);
                });

                $result[] = [
                    'category' => $day,
                    'value' => $values->isEmpty() ? 0 : round($values->avg(), 2),
                    'avg' => isset($average[$day]) ? round($average[$day], 2) : 0
                ];
            } else {
                // Day has no data
                $result[] = [
                    'category' => $day,
                    'value' => 0,
                    'avg' => isset($average[$day]) ? round($average[$day], 2) : 0
                ];
            }
        }

        return $result;
    }

    private function getPeriodAverage($period, $filterBy, $average)
    {
        switch ($filterBy) {
            case 'weekly':
                // $average is an array with day averages
                return isset($average[$period]) ? round($average[$period], 2) : 0;
                
            case 'monthly':
            case 'all_time':
                // $average is a single number
                return round($average, 2);
                
            default:
                return round($average, 2);
        }
    }

    private function formatPeriodForDisplay($period, $filterBy)
    {
        switch ($filterBy) {

            case 'weekly':
                return $period; // Monday, Tuesday, etc.

            case 'monthly':
                // Convert "2025-W03" to "Week 3, 2025"
                preg_match('/(\d{4})-W(\d{2})/', $period, $matches);
                return "Week {$matches[2]}, {$matches[1]}";

            case 'all_time':
                return Carbon::parse($period . '-01')->format('M Y'); // Jan 2025

            default:
                return $period;
        }
    }

    private function getSortOrder($period, $aggregation)
    {
        switch ($aggregation) {
            case 'weekly':
                $dayOrder = [
                    'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3,
                    'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7
                ];
                return $dayOrder[$period] ?? 8;

            case 'monthly':
                // Convert "2025-W03" to sortable format
                preg_match('/(\d{4})-W(\d{2})/', $period, $matches);
                return $matches[1] . $matches[2];

            case 'all_time':
                // Convert "2025-Q1" to sortable format
                preg_match('/(\d{4})-Q(\d)/', $period, $matches);
                return $matches[1] . '0' . $matches[2];

            default:
                return $period;
        }
    }

    public function getInsightsResponse(Request $request)
    {
        // aggregation = current Month, Past Month, Quarterly, Semi-Annualy, Annually
        // time_range = weekly, monthly, All Time

        $progressAggregation = $request->get('aggregation', 'current_month');
        $timeRange = $request->get('time_range', 'weekly');
        $emotion = $request->get('emotion', 'EDP-Stressful');

        // Validate parameters
        $validAggregations = ['current_month', 'past_month', 'quarterly', 'semi_annually', 'annually'];
        $validTimeRanges = ['weekly', 'monthly', 'all_time'];

        Log::info("EmloInsightsService - getInsightsResponse called with parameters: ", [
            'aggregation' => $progressAggregation,
            'time_range' => $timeRange,
            'emotion' => $emotion
        ]);

        if (!in_array($progressAggregation, $validAggregations)) {
            return response()->json(['error' => 'Invalid aggregation'], 400);
        }

        if (!in_array($timeRange, $validTimeRanges)) {
            return response()->json(['error' => 'Invalid time_range'], 400);
        }

        $emotionMap = [
            'EDP-Stressful' => 'stress',
            'EDP-Energetic' => 'energy',
            'EDP-Focused' => 'focus'
        ];

        $metricKey = $emotionMap[$emotion] ?? 'stress';

        // Base averages for each metric
        $baseAverages = [
            'stress' => 43,
            'energy' => 65,
            'focus' => 78
        ];

        $metricsData = [];
        $metricCategories = $this->getCategories($timeRange);
        foreach ($metricCategories as $category) {
            $value = rand(30, 100); // Random value between 30 and 100
            $avg = round($baseAverages[$metricKey] * (1 + (rand(-5, 5) / 100))); // ±5% variation
            $metricsData[] = [
                'category' => $category,
                'avg' => $avg,
                'value' => $value
            ];
        }

        // Static data for timeOfDayData
        $timeOfDayDataTemplate = [
            'stress' => ['morning' => rand(30, 60), 'afternoon' => rand(40, 70), 'evening' => rand(30, 60)],
            'energy' => ['morning' => rand(50, 80), 'afternoon' => rand(40, 70), 'evening' => rand(30, 60)],
            'focus' => ['morning' => rand(70, 90), 'afternoon' => rand(60, 80), 'evening' => rand(60, 80)]
        ];

        $timeOfDayData = $timeOfDayDataTemplate[$metricKey] ?? $timeOfDayDataTemplate['stress'];

        // Standardized secondaryMetrics as an array
        $secondaryMetrics = [
            [
                'name' => 'stressRecovery',
                'title' => 'Stress Recovery',
                'description' => 'Ability to return to calm after stress (Level 1 is best)',
                'currentValue' => rand(20, 30),
                'items' => [
                    ['range' => 1, 'label' => 'Excellent Recovery', 'percentage' => rand(10, 20)],
                    ['range' => 2, 'label' => 'Very Good', 'percentage' => rand(20, 30), 'isCurrent' => true],
                    ['range' => 3, 'label' => 'Good', 'percentage' => rand(15, 25)],
                    ['range' => 4, 'label' => 'Moderate', 'percentage' => rand(10, 20)],
                    ['range' => 5, 'label' => 'Needs Attention', 'percentage' => rand(5, 15)]
                ]
            ],
            [
                'name' => 'cognitiveBalance',
                'title' => 'Cognitive Balance',
                'description' => 'How well thoughts and emotions work together',
                'currentValue' => rand(20, 30),
                'items' => [
                    ['range' => '0.05-5', 'label' => 'Disconnected', 'percentage' => rand(10, 15)],
                    ['range' => '5-10', 'label' => 'Low Balance', 'percentage' => rand(20, 30), 'isCurrent' => true],
                    ['range' => '10-15', 'label' => 'Balanced', 'percentage' => rand(40, 50)],
                    ['range' => '15-17.5', 'label' => 'Overstimulated', 'percentage' => rand(10, 20)]
                ]
            ],
            [
                'name' => 'aggression',
                'title' => 'Aggression',
                'description' => 'How strongly anger comes through (0 is best)',
                'currentValue' => round((rand(0, 20) / 10), 1),
                'items' => [
                    ['range' => '0', 'label' => 'Best - No Aggression', 'percentage' => rand(60, 70)],
                    ['range' => '1-2', 'label' => 'Acceptable', 'percentage' => rand(25, 35), 'isCurrent' => true],
                    ['range' => '>2', 'label' => 'Needs Attention', 'percentage' => rand(5, 10)]
                ]
            ],
        ];

        $riskCurrentValue = rand(20, 30); // Random value for risk
        $risk = [
            'name' => 'risk',
            'title' => 'Risk (Self-Honesty)',
            'description' => 'How honest you\'re being with yourself (1-40 is best)',

            'min' => 1,
            'midpoint' => 40,
            'max' => 100,
            'currentValue' => $riskCurrentValue,

            'stats' => [
                ['label' => 'Current Level', 'value' => $riskCurrentValue, 'description' => 'Good Self-Honesty'],
                ['label' => 'Best Range', 'value' => '1-40', 'description' => 'Honest self-reflection'],
            ],
        ];

        // Progress over Time
        $progressData = [];
        $progressDataCategories = $this->getCategories($progressAggregation);

        foreach ($progressDataCategories as $category) {
            $value = rand(30, 100); // Random value between 30 and 100
            $avg = round($baseAverages[$metricKey] * (1 + (rand(-5, 5) / 100))); // ±5% variation
            $progressData[] = [
                'category' => $category,
                'avg' => $avg,
                'value' => $value
            ];
        }

        $daysOfWeek = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];
        $weeklyActivity = [];
        foreach ($daysOfWeek as $index => $day) {
            $weeklyActivity[] = [
                'day' => $day,
                'active' => rand(50, 100),
            ];
        }

        $userId = Auth::id();
        $stats = $this->getUserActivityStats($userId);

        $activity = [
            'weekly' => $weeklyActivity,
            'stats' => $stats
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Insights data retrieved successfully',
            'data' => [
                'metricsData' => $metricsData,
                'timeOfDayData' => $timeOfDayData,
                'secondaryMetrics' => $secondaryMetrics,
                'risk' => $risk,
                'activity' => $activity,
                'progressData' => $progressData ?? [],
            ],
        ]);
    }

    private function getCategories($type)
    {
        switch ($type) {
            // Time ranges
            case 'weekly':
                return ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            case 'monthly':
                return ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            case 'all_time':
                $now = Carbon::now();
                return [
                    $now->copy()->subMonths(5)->format('M'),
                    $now->copy()->subMonths(4)->format('M'),
                    $now->copy()->subMonths(3)->format('M'),
                    $now->copy()->subMonths(2)->format('M'),
                    $now->copy()->subMonths(1)->format('M'),
                    $now->format('M')
                ];

            // Aggregations
            case 'past_month':
            case 'current_month':
                return ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            case 'quarterly':
                return ['Q1', 'Q2', 'Q3', 'Q4'];
            case 'semi_annually':
                return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            case 'annually':
                return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            default:
                return ['Unknown'];
        }
    }

    public function getUserActivityStats($userId)
    {
        // Total number of check-ins (logins)
        $totalCheckIns = UserLogin::where('user_id', $userId)->count();

        // Check-ins for the current month
        $thisMonth = UserLogin::where('user_id', $userId)
            ->whereMonth('logged_in_at', Carbon::now()->month)
            ->whereYear('logged_in_at', Carbon::now()->year)
            ->count();

        // Distinct days with login (for streak calculation)
        $loginDays = UserLogin::where('user_id', $userId)
            ->orderBy('logged_in_at')
            ->pluck('logged_in_at')
            ->map(fn($dt) => Carbon::parse($dt)->toDateString())
            ->unique()
            ->values();

        // Streak calculation
        $currentStreak = 0;
        $longestStreak = 0;
        $streak = 0;
        $prev = null;

        foreach ($loginDays as $day) {
            if ($prev && Carbon::parse($prev)->diffInDays($day) === 1) {
                $streak++;
            } else {
                $streak = 1;
            }
            if ($streak > $longestStreak) {
                $longestStreak = $streak;
            }
            $prev = $day;
        }

        // If the last login was today, the current streak is valid
        $currentStreak = 0;
        if ($loginDays->count() && Carbon::parse($loginDays->last())->isToday()) {
            // Count how many consecutive days up to today
            $currentStreak = 1;
            for ($i = $loginDays->count() - 2; $i >= 0; $i--) {
                if (Carbon::parse($loginDays[$i])->diffInDays($loginDays[$i + 1]) === 1) {
                    $currentStreak++;
                } else {
                    break;
                }
            }
        }

        // Only video_requests that have related videos
        $totalRecordings = VideoRequest::where('user_id', $userId)
            ->whereHas('videos')
            ->count();

        $avgRecordings = $totalCheckIns > 0 ? round($totalRecordings / $totalCheckIns, 1) : 0;

        return [
            'avgRecordings' => $avgRecordings,
            'currentStreak' => $currentStreak,
            'longestStreak' => $longestStreak,
            'totalCheckIns' => $totalCheckIns,
            'thisMonth' => $thisMonth,
        ];
    }
}
