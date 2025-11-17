<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Models\VideoRequest;
use App\Models\Baseline;

use App\Services\Emlo\EmloInsights\EmloInsightsService;

class InsightsController extends Controller {

    public function __construct(protected EmloInsightsService $emloInsightsService,
    ){}

    public function getInsights(Request $request)
    {   
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $baselineRule = [
            'integer',
            Rule::exists('baselines', 'id')->where(function ($query) {
                $query->where('user_id', Auth::id());
            }),
        ];

        $request->validate([
            'baseline1_id' => $baselineRule,
            'baseline2_id' => $baselineRule,
        ], [
            'baseline1_id.exists' => 'The selected baseline does not exist or does not belong to you.',
            'baseline2_id.exists' => 'The selected baseline does not exist or does not belong to you.',
        ]);

        $baseline1 = Baseline::find($request->baseline1_id);
        $baseline2 = Baseline::find($request->baseline2_id);

        $routeName = Route::currentRouteName();

        $insightsData = $this->emloInsightsService->getInsightsData($routeName, $userId, 
                                                    $baseline1, $baseline2);

        return response()->json([
                'status' => 'success',
                'message' => 'Insights data retrieved successfully',
                'data' => $insightsData
            ]
        );
    }
}