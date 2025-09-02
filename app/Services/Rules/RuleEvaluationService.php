<?php

namespace App\Services\Rules;

use App\Models\EmloResponseParamSpecs;

use App\Exceptions\Emlo\EmloNotFoundException;

use App\Services\Rules\RulesEngineService;
use App\Services\Emlo\EmloResponseService;

class RuleEvaluationService
{
    public function __construct(
        private EmloResponseService $emloResponseService,
        protected RulesEngineService $rulesEngineService,
    ){}

    public function quickRuleCheck($paramValue, $allParamValues, $paramSpec)
    {
        $conditionsMet = $this->rulesEngineService->ruleCheck(
            $paramValue, 
            $allParamValues, 
            $paramSpec);

        return !empty($conditionsMet) ? $conditionsMet[0] : [];
    }

    public function evaluateRules(int $requestId, $userId, string $paramName, $queryOptions): array
    {
        $messages = [];
        
        $paramSpec = EmloResponseParamSpecs::select('id', 'param_name', 'distribution')
            ->where("param_name", $paramName)
            ->first();
        if (!$paramSpec) {
            throw new EmloNotFoundException("EMLO param specification for param '{$paramName}' not found");
        }

        $paramValues = $this->emloResponseService->getAllValuesOfParam($paramSpec->param_name, $userId, $queryOptions);
        $paramValue = $this->emloResponseService->getParamValueByRequestId($requestId, $userId, $paramSpec->param_name);

        $conditionsMet = $this->rulesEngineService->ruleCheck($paramValue, $paramValues, $paramSpec);
        if ($conditionsMet) {
            foreach ($conditionsMet as $conditionMet) {
                $messages [] = $conditionMet->message ?? '';
            }
        } else {
            return [];
        }
        return $messages;
    }
}