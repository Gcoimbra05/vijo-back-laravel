<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Exceptions\Emlo\ParamaterNotFoundException;
use App\Exceptions\Emlo\EmloNotFoundException;

use App\Models\VideoRequest;

use App\Services\Emlo\EmloInsights\EmloInsightsService;

class AiAgentController extends Controller {

    public function __construct(
        protected EmloInsightsService $emloInsightsService,
    ){}

    public function getSingleParamEmotionalInsights($paramName)
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $videoRequest = VideoRequest::latestWithVideoAndParamAggregates($userId);

        try {
            $paramInsights = $this->emloInsightsService->getInsightsDataForSingleParam(
                $userId, 
                $videoRequest->id, 
                $paramName
            );

            return response()->json([
                'success' => true,
                'message' => 'Emotion details retrieved successfully.',
                'data' => $paramInsights
            ], 200);

        } catch (ParamaterNotFoundException $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Emotion' . $paramName . 'does not exist.',
                'data' => null
            ], 404);

        } catch (EmloNotFoundException $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'data' => null
            ], 500);
        }
    }
}