<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmloResponse;
use App\Services\Emlo\EmloResponseService;
use App\Services\Emlo\EmloHelperService;

class EmloResponseController extends Controller {

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

    public function getEmloResponseParamValue(Request $request, $param_name)
    {
        // Get query parameters
        $response_id = $request->query('response_id');
        $start_time = $request->query('start_time');
        $end_time = $request->query('end_time');
    
        // Get ordering parameters
        $orderBy = $request->query('orderby', 'created_at');
        $direction = $request->query('direction', 'DESC');
            
        // Build filters array (only include non-empty values)
        $filters = [];
        if (!empty($response_id)) $filters['response_id'] = $response_id;

        // Get pagination parameters
        $limit = (int)$request->query('limit', 20);
        $offset = (int)$request->query('offset', 0);
        
        // Call service method with time parameters
        $result = EmloResponseService::getEmloResponseParamValue(
            $param_name,
            $filters,
            $limit,
            $offset,
            $orderBy,
            $direction,
            $start_time,
            $end_time
        );
        
        // Return JSON response
        return response()->json($result);
    }

}