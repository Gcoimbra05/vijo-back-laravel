<?php

namespace App\Services;


use App\Models\Rule;
use Illuminate\Support\Collection;
use App\Models\EmloResponseParamSpecs;
use App\Services\Emlo\EmloResponseService;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Emlo\EmloNotFoundException;
use App\Exceptions\Emlo\NoRulesFoundException;
use App\Exceptions\Emlo\NotEnoughEmloParamValuesException;


class RuleEvaluationService
{
    public function __construct(private EmloResponseService $emloResponseService
    ){}

    public function evaluateRules(int $requestId, $userId, string $paramName, $queryOptions): array
    {
        $paramsWValues = [];
        $messages = [];
        
        $paramSpec = EmloResponseParamSpecs::select('id', 'param_name', 'distribution')
            ->where("param_name", $paramName)
            ->first();
        if (!$paramSpec) {
            throw new EmloNotFoundException("EMLO param specification for param '{$paramName}' not found");
        }

        $rule = Rule::with('conditions')
            ->where('param_spec_id', $paramSpec->id)
            ->where('active', true)
            ->first();
        if (!$rule) {
            throw new NoRulesFoundException("No rules found for given EMLO parameter");
        }

        $conditionParams = self::getOtherParamsNeededForConditions($rule->conditions, $paramSpec->param_name);
        $paramDisributions = self::getDistributionTypesForConditionParams($conditionParams);
        $paramDisributions [] = ["param" => $paramSpec->param_name, "distribution" => $paramSpec->distribution];

        foreach ($paramDisributions as $distribution) {
            if ($distribution['distribution'] == 'gaussian') {
                $paramValues = $this->emloResponseService->getAllValuesOfParam($distribution['param'], $userId, $queryOptions);
                if (!$paramValues) {
                    throw new EmloNotFoundException("EMLO param values not found for param '{$distribution['param']}");
                }

                $standardDeviation = self::standardDeviation($paramValues);
                Log::info("standard deviation for param '{$distribution['param']}' is '{$standardDeviation}'");
                $paramsWValues[$distribution['param']] = $standardDeviation;

            } else if ($distribution['distribution'] == 'definitive_state') {
                $paramValue = $this->emloResponseService->getParamValueByRequestId($requestId, $userId, $paramSpec->param_name);
                if (!$paramValue) {
                    throw new EmloNotFoundException("EMLO param value not found for param '{$distribution['param']}");
                }
                $paramsWValues[$distribution['param']] = $paramValue;
            }
        }
        
        foreach ($rule->conditions as $condition) {
            $conditionResult = $this->evaluateCondition($condition->condition, $paramsWValues);
            if ($conditionResult) {
                $messages[] = $condition->message;
            }

        }

        return $messages;
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