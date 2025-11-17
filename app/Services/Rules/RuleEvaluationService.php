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
}