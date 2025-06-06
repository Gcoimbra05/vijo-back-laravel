<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access.',
                'results' => [
                    'affiliates' => null
                ],
            ], 401);
        }

        $affiliates = Affiliate::all();

        return response()->json([
            'status' => true,
            'message' => 'Affiliates retrieved successfully.',
            'results' => [
                'affiliates' => $affiliates
            ],
        ]);
    }

    public function show($id)
    {
        $affiliate = Affiliate::find($id);
        if (!$affiliate) {
            $responseData = [
                'status'  => false,
                'message' => "Affiliate not found.",
                'results' => [
                    'affiliate' => null
                ]
            ];
        } else {
            $responseData = [
                'status'  => true,
                'message' => "Affiliate retrieved successfully.",
                'results' => [
                    'affiliate' => $affiliate
                ]
            ];
        }

        return response()->json($responseData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:regular,master,corporate',
            'status' => 'required|string|in:active,inactive',
        ]);

        $data = $request->all();
        $data['creator_id'] = Auth::id();

        $affiliate = Affiliate::create($data);

        if($affiliate){
            $responseData = [
                'status'  => true,
                'message' => "Affiliate created successfully.",
                'results' => [
                    'affiliate' => $affiliate
                ]
            ];
        } else {
            $responseData = [
                'status'  => false,
                'message' => "Failed to create affiliate.",
                'results' => [
                    'affiliate' => null
                ]
            ];
        }

        return response()->json($responseData, $affiliate ? 201 : 400);
        
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'sometimes|integer|exists:users,id',
            'type' => 'sometimes|string|in:regular,master,corporate',
            'status' => 'sometimes|string|in:active,inactive',
        ]);

        $affiliate = Affiliate::find($id);
        if (!$affiliate) {
            $responseData = [
                'status'  => false,
                'message' => "Affiliate not found.",
                'results' => [
                    'affiliate' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $affiliate->update($request->all());

        $responseData = [
            'status'  => true,
            'message' => "Affiliate updated successfully.",
            'results' => [
                'affiliate' => $affiliate
            ]
        ];

        return response()->json($responseData);
    }

    public function destroy($id)
    {
        $affiliate = Affiliate::find($id);
        if (!$affiliate) {
            $responseData = [
                'status'  => false,
                'message' => "Affiliate not found.",
                'results' => [
                    'affiliate' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $affiliate->delete();

        $responseData = [
            'status'  => true,
            'message' => "Affiliate deleted successfully.",
            'results' => [
                'affiliate' => null
            ]
        ];

        return response()->json($responseData, 200);
    }
}