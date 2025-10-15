<?php

namespace App\Services\Rules;

use App\Models\Rule;
use App\Models\EmloResponseParamSpecs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Emlo\NotEnoughEmloParamValuesException;
use App\Models\RuleCondition;
use Exception;

class RulesEngineService {
    public function ruleCheck($paramValue, $allValuesOfParam, $paramSpec)
    {
        $rule = Rule::with('conditions')
            ->where('param_spec_id', $paramSpec->id)
            ->where('active', true)
            ->first();
        if (!$rule) {
            return [];
        }

        $longMessage = "";
        $conditionsMet = [];
        
        if (!$paramSpec->secondary) {
            $statusInfo = $this->getStatusInfo($paramValue,$allValuesOfParam);
            $shortMessage = $statusInfo['message'];

            if ($statusInfo['order_index'] != null) {
                $message = RuleCondition::select('message')
                    ->where('rule_id', $rule->id)
                    ->where('order_index', $statusInfo['order_index'])
                    ->first();
                $longMessage = $message?->message;
            }

            $conditionMet = (object) [
                "message" => $longMessage,
                "emotion_performance" => $shortMessage
            ];

            $conditionsMet [] = $conditionMet;



        } else {
            $statusInfo = $this->getStatusInfo($paramValue, $allValuesOfParam);
            $shortMessage = $statusInfo['message'];

            $conditionParams = self::getOtherParamsNeededForConditions($rule->conditions, $paramSpec->param_name);
            $paramDisributions = self::getDistributionTypesForConditionParams($conditionParams);
            $paramDisributions [] = ["param" => $paramSpec->param_name, "distribution" => $paramSpec->distribution];

            $paramsWValues = [];
            foreach ($paramDisributions as $distribution) {
                $paramsWValues[$distribution['param']] = $paramValue;
            }

            foreach ($rule->conditions as $condition) {
                $conditionResult = $this->evaluateCondition($condition->condition, $paramsWValues);
                if ($conditionResult) {
                    $conditionMet = (object) [
                        "message" => $condition->message,
                        "emotion_performance" => $shortMessage,
                        "emotion_performance_secondary" => $condition->emotion_performance
                    ];

                    $conditionsMet [] = $conditionMet;
                }

            }
        }

        Log::debug("conditions met are: " . json_encode($conditionsMet));
        return $conditionsMet;
    }

    private function getStatusInfo($singleValueOfParam , $allValuesOfParam)
    {
        $standardDeviation = self::standardDeviation($allValuesOfParam->pluck('value'));
        $mean = self::mean($allValuesOfParam);
        $statusInfo = $this->evaluateStandardDeviation($singleValueOfParam, $mean,$standardDeviation);
        return $statusInfo;
    }

    private function evaluateOperator($leftValue, $operator, $rightValue): bool
    {
        switch ($operator) {
            case '>':
                return $leftValue > $rightValue;
            case '<':
                return $leftValue < $rightValue;
            case '>=':
                return $leftValue >= $rightValue;
            case '<=':
                return $leftValue <= $rightValue;
            case '=':
                return $leftValue == $rightValue;
            case '!=':
                return $leftValue != $rightValue;
            case 'between':
                return $leftValue >= $rightValue['min'] && $leftValue <= $rightValue['max'];
            case 'in':
                return in_array($leftValue, $rightValue);
            case 'not_in':
                return !in_array($leftValue, $rightValue);
            default:
                return false;
        }
    }

    private function evaluateStandardDeviation($value, $mean, $standardDeviation)
    {
        // Calculate threshold boundaries
        $lowerThreshold = $mean - (0.5 * $standardDeviation);  // -0.5 SD
        $upperThreshold = $mean + (0.5 * $standardDeviation);  // +0.5 SD

        switch (true) {
            case ($value < $lowerThreshold):
                return ["message" => "Below Average", "order_index" => 1];

            case ($value <= $upperThreshold):
                return ["message" => "Average", "order_index" => 2];

            case ($value > $upperThreshold):
                return ["message" => "Above Average", "order_index" => 3];

            default:
                return ["message" => "", "order_index" => null];
        }
    }

    private function evaluateCondition(array $condition, array $params): bool
    {
        $metConditions = 0;

        if($condition['type'] == 'compound') {
            
            foreach($condition['conditions'] as $index => $singleCondition){
        
                if (!isset($params[$singleCondition['param']])) {
                    Log::warning("Parameter missing", ['param' => $singleCondition['param']]);
                    continue;
                }

                $result = $this->evaluateOperator($params[$singleCondition['param']], $singleCondition['operator'], $singleCondition['value']);
                if($result){
                    $metConditions++;
                }
            }

            if (count($condition['conditions']) == $metConditions){
                return true;
            } else {
                return false;
            }

        } else {
            if (!isset($condition['param'], $condition['operator'], $condition['value'])) {
                Log::error('Invalid simple condition structure', ['condition' => $condition]);
                return false;
            }

            if (!isset($params[$condition['param']])) {
                Log::warning("Parameter missing for simple condition", ['param' => $condition['param']]);
                return false;
            }

            $result = $this->evaluateOperator($params[$condition['param']], $condition['operator'], $condition['value']);
            
            Log::info('Simple condition result', [
                'result' => $result ? 'PASSED' : 'FAILED'
            ]);
            
            return $result;
        }
    }

    private static function getOtherParamsNeededForConditions($conditions, $mainParamName) {

        $paramsInConditions = [];

        foreach ($conditions as $condition) {
            if ($condition->condition['type'] === 'compound') {
                foreach($condition->condition['conditions'] as $subCondition) {
                    if ((!in_array($subCondition['param'], $paramsInConditions)) && $subCondition['param'] != $mainParamName) {
                        $paramsInConditions[] = $subCondition['param'];
                    }
                }
            } else {
                if ((!in_array($condition->condition['param'], $paramsInConditions)) && $condition->condition['param'] != $mainParamName) {
                    $paramsInConditions[] = $condition->condition['param'];
                }
            }
        }

        return $paramsInConditions;
    }

    private static function getDistributionTypesForConditionParams($conditionParams) {
        $paramDistributions = [];

        foreach($conditionParams as $conditionParam) {
            $distribution = EmloResponseParamSpecs::select('distribution')
                ->where('param_name', $conditionParam)
                ->first();
            if ($distribution) {
                $paramDistributions [] = [ "param" => $conditionParam, "distribution" => $distribution->distribution ];
            }
            
        }
        return $paramDistributions;
    }

    public static function standardDeviation(Collection $numbers, bool $sample = false): float
    {
        // DEBUG: See what you're actually getting
        Log::info('Original values:', $numbers->toArray());
        
        // Convert all values to numbers and filter out non-numeric values
        $numericValues = $numbers->filter(function ($value) {
            $isNumeric = is_numeric($value);
            Log::info("Value: " . var_export($value, true) . " | is_numeric: " . ($isNumeric ? 'true' : 'false'));
            return $isNumeric;
        })->map(function ($value) {
            return (float) $value;
        });
        
        Log::info('Filtered count:', [$numericValues->count()]);
        
        $count = $numericValues->count();
    
        if ($count === 0) {
            throw new Exception('No numeric values provided');
        }
        
        if ($sample && $count < 2) {
            throw new NotEnoughEmloParamValuesException('at least 2 values of EMLO param are required to calculate standard deviation');
        }

        // Calculate mean
        $mean = $numericValues->avg();

        // Calculate variance
        $variance = $numericValues->map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        })->sum();

        // Divide by n for population, n-1 for sample
        $divisor = $sample ? $count - 1 : $count;
        $variance = $variance / $divisor;

        // Return standard deviation (square root of variance)
        return sqrt($variance);
    }

    public static function mean(Collection $numbers)
    {   
        $numericValues = $numbers->filter(function ($value) {
                return is_numeric($value);
            })->map(function ($value) {
                return (float) $value;
            });
        return $numericValues->avg();
    }
}