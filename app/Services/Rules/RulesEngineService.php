<?php

namespace App\Services\Rules;

use App\Models\Rule;
use App\Models\EmloResponseParamSpecs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Emlo\NotEnoughEmloParamValuesException;
use App\Models\RuleCondition;

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
            $statusInfo = $this->getStatusInfo($allValuesOfParam);
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
            $statusInfo = $this->getStatusInfo($allValuesOfParam);
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

    private function getStatusInfo($allValuesOfParam)
    {
        $standardDeviation = self::standardDeviation($allValuesOfParam);
        $statusInfo = $this->evaluateStandardDeviation($standardDeviation);
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

    private function evaluateStandardDeviation($value)
    {
        switch (true) {
            case ($value > -1.5 && $value < -0.5):
                return ["message" => "Below Average", "order_index" => 1];
            
            case ($value > -0.5 && $value < 0.5):
                return ["message" => "Average", "order_index" => 2];

            case ($value > 0.5 && $value < 1.5):
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
        $count = $numbers->count();
        if ($sample && $count < 2) {
            throw new NotEnoughEmloParamValuesException('at least 2 values of EMLO param are required to calculate standard deviation');
        }

        // Convert all values to numbers and filter out non-numeric values
        $numericValues = $numbers->filter(function ($value) {
            return is_numeric($value);
        })->map(function ($value) {
            return (float) $value;
        });

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
}