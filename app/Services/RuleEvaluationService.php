<?php

namespace App\Services;

use App\Models\Rule;
use App\Models\EmloResponseParamSpecs;
use App\Services\Emlo\EmloResponseService;

use Illuminate\Support\Facades\Log;

class RuleEvaluationService
{
    public function evaluateRules(int $requestId, string $paramName): array
    {
       
        $paramsWValues = [];
        $emloResponseService = app(EmloResponseService::class);

        $rules = Rule::with('conditions')
            ->where('param_name', $paramName)
            ->where('active', true)
            ->get();
        
        $paramsWSpec = $this->getAllRequestParamsWSpec();

        foreach ($paramsWSpec as $param) {

            $paramWValues = $emloResponseService->getEmloResponseParamValueForId($requestId, $param->param_name);
            
            if($paramWValues['success'] == true){
                
                $dataPoint = $this->extractRelevantValueFromParamArray($paramWValues);
                if ($dataPoint) {
                    $paramsWValues[$param->param_name] = $dataPoint;
                } else {
                    return [];
                }
                
            }
        }

        $messages = [];
                
        foreach ($rules as $rule) {
            foreach ($rule->conditions as $condition) {
                
                $conditionResult = $this->evaluateCondition($condition->condition, $paramsWValues);

                if ($conditionResult) {
                    $messages[] = $condition->message;
                }
            }
        }

        return $messages;
    }

    private function getAllRequestParamsWSpec()
    {
        $paramsWSpecs = EmloResponseParamSpecs::all();
        return $paramsWSpecs;

    }

    private function extractRelevantValueFromParamArray(array $paramArray)
    {
        if(isset($paramArray['data'])) {
            if(isset($paramArray['data'][0]['numeric_value'])) {
                return $paramArray['data'][0]['numeric_value'];
            } else {
                $stringValue = json_decode($paramArray['data'][0]['string_value'], true);
                if(isset($stringValue)){
                    return $stringValue['averageLevel'];
                } else {
                    return false;
                }
            }
        }
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
            case '==':
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

}