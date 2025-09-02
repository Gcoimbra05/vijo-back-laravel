<?php

namespace App\Http\Controllers;

use App\Exceptions\Emlo\EmloNotFoundException;
use App\Exceptions\Emlo\NoRulesFoundException;
use App\Services\QueryParamsHelperService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Services\Rules\RuleEvaluationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Exceptions\UserNotFoundException;


class RuleEvaluationController extends Controller
{

    public function __construct(private RuleEvaluationService $ruleEvaluationService
    ){}

    public function evaluateRules(Request $request, $requestId, string $paramName)
    {
        try {

            $userId = Auth::id();

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

            $evalResult = $this->ruleEvaluationService->evaluateRules($requestId, $userId, $paramName, $queryOptions);
            return response()->json($evalResult);
        } catch (EmloNotFoundException) {
            return response()->json(['message' => 'insights values not found'], 404);
        }  catch (NoRulesFoundException) {
            return response()->json(['message' => 'no rules found for EMLO parameter'], 404);
        }  catch (UserNotFoundException) {
            return response()->json(['message' => 'user not found'], 404);
        } catch (\Exception $e) {
            Log::error('error is: ' . $e->getTraceAsString());
            return response()->json(['message' => 'internal server error'], 500);
        
        }
    }
}