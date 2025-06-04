<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\RuleEvaluationService;


class RuleEvaluationController extends Controller
{

    public function __construct(private RuleEvaluationService $ruleEvaluationService)
    {

    }

    public function evaluateRules(int $requestId, string $paramName)
    {

        if ($requestId <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid request ID'
            ], 400);
        }
    
        if (empty(trim($paramName))) {
            return response()->json([
                'status' => false,
                'message' => 'Parameter name cannot be empty'
            ], 400);
        }

        $evalResult = $this->ruleEvaluationService->evaluateRules($requestId, $paramName);
        return response()->json($evalResult);
    }

}