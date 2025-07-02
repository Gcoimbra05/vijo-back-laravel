<?php

namespace App\Http\Controllers;

use App\Exceptions\EmloNotFoundException;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\RuleEvaluationService;
use Illuminate\Support\Facades\Log;


class RuleEvaluationController extends Controller
{

    public function __construct(private RuleEvaluationService $ruleEvaluationService
    ){}

    public function evaluateRules(int $requestId, string $paramName)
    {
        try {
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
        } catch (EmloNotFoundException) {
            return response()->json(['error' => 'insights values not found'], 404);
        } catch (\Exception) {
            return response()->json(['error' => 'internal server error'], 500);
        }
    }
}