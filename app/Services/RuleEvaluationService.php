<?php

namespace App\Services;

use App\Models\Rule;
use App\Models\EmloResponseParamSpecs;
use App\Services\Emlo\EmloResponseService;
use Illuminate\Support\Facades\Log;

use App\Exceptions\EmloParamSpecNotFoundException;

class RuleEvaluationService
{
    public function __construct(private EmloResponseService $emloResponseService
    ){}

    public function evaluateRules(int $requestId, string $paramName): array
    {
        $paramsWValues = [];

        $paramSpecId = EmloResponseParamSpecs::select('id')
            ->where("simplified_param_name", $paramName)
            ->first();
        if (!$paramSpecId) {
            throw new EmloParamSpecNotFoundException("EMLO param specification id for param '{$paramName}' not found");
        }

        $rules = Rule::with('conditions')
            ->where('param_spec_id', $paramSpecId->id)
            ->where('active', true)
            ->get();
        
        $paramWSpec = EmloResponseParamSpecs::select('param_name')
            ->where('simplified_param_name', $paramName)
            ->first();
        if (!$paramWSpec) {
            throw new EmloParamSpecNotFoundException("EMLO param specification simplified_param_name for param '{$paramName}' not found");
        }

        $paramValue = $this->emloResponseService->getParamValueByRequestId($requestId, $paramWSpec->param_name);
        $dataPoint = $paramValue;
        if ($dataPoint) {
            $paramsWValues[$paramWSpec->param_name] = $dataPoint;
        } else {
            return [];
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

}