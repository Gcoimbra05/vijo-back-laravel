<?php

namespace App\Services\Emlo\EmloInsights;

use App\Services\Emlo\EmloHelperService;
use App\Services\Emlo\EmloResponseService;
use App\Models\EmloResponseValue;
use App\Models\EmloResponse;
use App\Services\Emlo\EmloSegmentParameterService;
use Illuminate\Support\Facades\Log;
class SecondaryMetricsService {

    public function __construct(
        protected EmloResponseService $emloResponseService,
        protected EmloSegmentParameterService $emloSegmentService){}



    public function getSecondaryMetrics($userId)
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
        Log::debug('AGGRO PERCENTAGES: ' . json_encode($aggressionPercentages));
        $aggressionInfoArray = $this->createAggressionInfoArray($aggressionPercentages);

        $returnArray = [
            $clStressInfoArray,
            $ocaInfoArray,
            $aggressionInfoArray
        ];

        return $returnArray;
    }

    public function getOcaInfo($oCA)
    {
        foreach ($oCA as $ocaValue) {
            $normalizedValue = EmloHelperService::applyNormalizationFormula($ocaValue->value);
            $ocaValue->value = $normalizedValue;
        }
        $oCAPercentages = $this->sortOCAByValue($oCA);
        $ocaInfoArray = $this->createOCAInfoArray($oCAPercentages);
        return $ocaInfoArray;
    }

    public function getClStressInfo($clStress)
    {
        $clStressPercentages = $this->sortClStressByValue($clStress);
        $clStressInfoArray = $this->createClStressInfoArray($clStressPercentages);
        return $clStressInfoArray;
    }

    public function getAggresionInfo($aggression)
    {
        $aggressionPercentages = $this->sortAggressionByValue($aggression);
        $aggressionInfoArray = $this->createAggressionInfoArray($aggressionPercentages);
        return $aggressionInfoArray;
    }

    public function getSelfHonestyInfo($userId)
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
        
        $avg = $this->emloSegmentService->calculateAverageOfSingleResponse('finalRiskLevel', $response->raw_response);
        $selfHonesty = round(100 - $avg);

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
            "description" => "How honest you're being with yourself (60-100 is best)",
            "min" => 1,
            "midpoint"=> 60,
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

    private function getSecondaryMetricPercentages($sortedValues)
    {
        $sortedValues = array_values($sortedValues); // Re-index from 0
        $total = array_sum($sortedValues);
        
        if ($total == 0) {
            return $sortedValues;
        }
        
        $percentages = [];
        foreach ($sortedValues as $key => $count) {
            $percentages[$key] = round(($count / $total) * 100);
        }
        
        return $percentages;
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
                if ($index == 0) $newestValue = round($clStressValue->value);

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
                if ($index == 0) $newestValue = round($oCAValue->value);

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
                if ($index == 0) $newestValue = round($aggressionValue->value);

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
                "description" => "Ability to return to calm after stress (Level 1 is best)",
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
                "description" => "How well thoughts and emotions work together",
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
                        "label"=> "Optimal",
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

}