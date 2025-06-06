<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\ReferralCode;
use Illuminate\Support\Facades\Auth;

class ReferralCodeController extends  Controller
{

    public function index(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.',
                'data' => null,
            ], 401);
        }

        $perPage = $request->query('limit', 15);
        $page = (int) $request->query('page', 1);
        
        $sortBy = $request->query('sort_by', 'start_date');
        $sortOrder = $request->query('sort_order', 'asc');
        
        $allowedSortColumns = ['start_date', 'end_date', 'code', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'start_date';
        }
        
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';

        $query = ReferralCode::orderBy($sortBy, $sortOrder);

        $refCodes = $query->skip($perPage * ($page - 1))->take($perPage)->get();

        return response()->json([
            'success' => true,
            'message' => 'Referral codes retrieved successfully.',
            'data' => $refCodes,
        ]);
    }

    public function store(Request $request)
    {
        $code = $request->code;
        if(!$code){
            $code = ReferralCode::generateReferralCode();
        }

        $request->validate([
            'affiliate_id' => 'required|exists:affiliates,id',
            'code' => 'unique:referral_codes',
            'commission' => 'numeric|min:0',
            'max_number_uses' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['code'] = $code;

        $ref_code = ReferralCode::create($data);

        if($ref_code){
            $responseData = [
                'status'  => true,
                'message' => "",
                'results' => [
                    'referral_code' => $ref_code
                ]
            ];
        } else {
            $responseData = [
                'status'  => false,
                'message' => "Failed to create referral code.",
                'results' => [
                    'referral_code' => null
                ]
            ];
        }

        return response()->json($responseData, $ref_code ? 201 : 400);
        
    }

    public function show($id)
    {
        $plan = ReferralCode::find($id);

        if (!$plan) {
            $responseData = [
                'status'  => false,
                'message' => "Referral code not found.",
                'results' => [
                    'referral_code' => null
                ]
            ];
        } else {
            $responseData = [
                'status'  => true,
                'message' => "",
                'results' => [
                    'referral_code' => $plan
                ]
            ];
        }

        return response()->json($responseData);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'affiliate_id' => 'required|exists:affiliates,id',
            'code' => 'sometimes|unique:referral_codes',
            'commission' => 'numeric|min:0',
            'max_number_uses' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'end_date' => 'sometimes|date',
        ]);

        $refCode = ReferralCode::find($id);

        if (!$refCode) {
            $responseData = [
                'status'  => false,
                'message' => "Referral code not found.",
                'results' => [
                    'referral_code' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $refCode->update($request->all());

        $responseData = [
            'status'  => true,
            'message' => "",
            'results' => [
                'referral_code' => $refCode
            ]
        ];

        return response()->json($responseData);
    }

    public function destroy($id)
    {
        $refCode = ReferralCode::find($id);

        if (!$refCode) {
            $responseData = [
                'status'  => false,
                'message' => "Referral code not found.",
                'results' => [
                    'referral_code' => null
                ]
            ];
            return response()->json($responseData, 404);
        }

        $refCode->delete();

        $responseData = [
            'status'  => true,
            'message' => "Referral code deleted successfully.",
            'results' => [
                'referral_code' => null
            ]
        ];

        return response()->json($responseData, 200);
    }

}