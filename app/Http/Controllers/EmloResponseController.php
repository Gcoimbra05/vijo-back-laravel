<?php

namespace App\Http\Controllers;

use App\Services\Emlo\EmloInsights\SnapshotService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmloResponse;
use App\Services\Emlo\EmloResponseService;
use App\Services\Emlo\EmloHelperService;
use App\Services\Emlo\EmloInsights\EmloInsightsService;

use App\Exceptions\Emlo\EmloNotFoundException;
use App\Models\VideoRequest;

class EmloResponseController extends Controller {

    public function __construct(
        protected EmloResponseService $emloResponseService, 
        protected EmloInsightsService $emloInsightsService,
        protected SnapshotService $snapshotService
    ){}

    public function index()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.',
                'results' => [
                    'emlo_responses' => null
                ],
            ], 401);
        }

        $emloResponses = EmloResponse::all();

        return response()->json([
            'status' => true,
            'message' => 'Emlo responses retrieved successfully.',
            'results' => [
                'emlo_responses' => $emloResponses
            ],
        ]);
    }

    public function show($id)
    {
        $emloResponse = EmloResponse::find($id);
        if (!$emloResponse) {
            $responseData = [
                'status'  => false,
                'message' => "Emlo response not found.",
                'results' => [
                    'emlo_response' => null
                ]
            ];
        } else {
            $responseData = [
                'status'  => true,
                'message' => "Emlo response retrieved successfully.",
                'results' => [
                    'emlo_response' => $emloResponse
                ]
            ];
        }

        return response()->json($responseData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer|exists:video_requests,id',
            'raw_response' => 'required',
        ]);

        $data = $request->all();

        $emloResponse = EmloResponse::create($data);

        if($emloResponse){
            $rawResponseString = is_string($request->raw_response) ? $request->raw_response : json_encode($request->raw_response);
            
            $result = EmloHelperService::extractAndStorePathValues($rawResponseString, $emloResponse->id);
            if (!$result['success']) {
                if (isset($result['errors'])) {
                    return response()->json(['status' => false, 'message' => $result['errors']], 422);
                }
                return response()->json(['status' => false, 'message' => 'Failed to create EMLO response'], 500);
            }

            $this->emloResponseService->storeSegmentParameters($emloResponse->id, $request->raw_response);
        
            $responseData = [
                    'status'  => true,
                    'message' => "Emlo response stored successfully.",
                    'results' => [
                        'emlo_response' => $emloResponse
                    ]
                ];

            return response()->json($responseData, 201);

        } else {
            $responseData = [
                'status'  => false,
                'message' => "Failed to store emlo response.",
                'results' => [
                    'emlo_response' => null
                ]
            ];
        }

        return response()->json($responseData, $emloResponse ? 201 : 400);
        
    }

    public function destroy($id)
    {
        $emloResponse = EmloResponse::find($id);
        if (!$emloResponse) {
            $responseData = [
                'status'  => false,
                'message' => "Emlo response not found.",
                'results' => [
                    'emlo_response' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $emloResponse->delete();

        $responseData = [
            'status'  => true,
            'message' => "Emlo response deleted successfully.",
            'results' => [
                'emlo_response' => null
            ]
        ];

        return response()->json($responseData, 200);
    }

    public function getParamValueByRequestId(Request $request, $requestId, $paramName)
    {
        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json(['error' => 'user not found'], 404);
            }

            $result = $this->emloResponseService->getParamValueByRequestId($requestId, $userId, $paramName);
            return response()->json($result);
        } catch (EmloNotFoundException) {
            return response()->json(['error' => 'parameter value not found'], 404);
        } catch (\Exception) {
            return response()->json(['error' => 'internal server error'], 500);
        }
    }

    public function getEmotionalSnapshot()
    {
        $userId = Auth::id();
        if (!$userId) {
                return response()->json([
                'success' => false,
                'message' => 'User not found',
                'data' => [],
            ], 404);
        }

        $request = VideoRequest::latestWithVideoAndParamAggregates($userId);

        $snapshot = $lastVijo = null;
        if ($request){
            $snapshot = $this->snapshotService->getEmotionalSnapshot($request->id);
            $lastVijo = [
                'title' => $request->title,
                'date' => $request->created_at->format('M d, Y'),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Emotional snapshot retrieved successfully.',
            'data' => $snapshot,
            'last_vijo' => $lastVijo,
        ], 200);
    }
}