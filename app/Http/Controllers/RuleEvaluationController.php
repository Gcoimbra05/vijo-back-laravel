<?php

namespace App\Http\Controllers;

use App\Exceptions\EmloNotFoundException;
use App\Exceptions\NoRulesFoundException;
use App\Services\QueryParamsHelperService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\RuleEvaluationService;
use Exception;
use Illuminate\Support\Facades\Log;


class RuleEvaluationController extends Controller
{

    public function __construct(private RuleEvaluationService $ruleEvaluationService
    ){}

    public function evaluateRules(Request $request, $requestId, string $paramName)
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

            $queryOptions = QueryParamsHelperService::getQueryOptions($request);

            $evalResult = $this->ruleEvaluationService->evaluateRules($requestId, $paramName, $queryOptions);
            return response()->json($evalResult);
        } catch (EmloNotFoundException) {
            return response()->json(['message' => 'insights values not found'], 404);
        } catch (\Exception $e) {
            Log::error('error is: ' . $e->getTraceAsString());
            return response()->json(['message' => 'internal server error'], 500);
        }  catch (NoRulesFoundException) {
            return response()->json(['message' => 'no rules found for EMLO parameter'], 404);
        }
    }
}