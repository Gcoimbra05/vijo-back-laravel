<?php

namespace App\Http\Controllers;

use App\Exceptions\CouponNotFoundException;
use App\Exceptions\StripeCouponNotFoundException;
use App\Models\Affiliate;
use App\Models\Coupon;

use App\Services\CouponManagerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class CouponController {

    public function __construct(
        protected CouponManagerService $couponManagerService,
    ){}

    public function index()
    {
        $userId = Auth::id();
        $affiliate = Affiliate::select('*')->where('user_id', $userId)->first();

        $completeCoupons = $this->couponManagerService->getAllForAffiliate($affiliate, $userId);
        
        return response()->json([
            'success' => true,
            'message' => 'Coupons retrieved successfully.',
            'data' => $completeCoupons,
        ]);
    }

    public function show($id)
    {
        $userId = Auth::id();
        $affiliate = Affiliate::select('*')->where('user_id', $userId)->first();

        try {
            $coupon = $this->couponManagerService->getSingleForAffiliate($id, $userId, $affiliate);
        } catch (CouponNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found or doesn\'t belong to user.',
                'data' => null,
            ], 404);

        } catch (StripeCouponNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error.',
                'data' => null,
            ], 500);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Coupon retrieved successfully.',
            'data' => $coupon,
        ], 200);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'affiliate_id' => 'required|integer|exists:affiliates,id',
            'amount_off' => 'required_without:percent_off|nullable|integer|min:1',
            'percent_off' => 'required_without:amount_off|nullable|numeric|min:1|max:100',
            'duration' => 'string|in:once,repeating,forever',
            'duration_in_months' => 'required_if:duration,repeating|nullable|integer|min:1',
            'name' => 'string|max:20',
            'max_redemptions' => 'integer',
            'redeem_by' => 'timestamp'
        ]);

        $data = $request->only([
            'amount_off',
            'duration',
            'duration_in_months',
            'name',
            'max_redemptions',
            'redeem_by',
            'percent_off',
        ]);

        $coupon = $this->couponManagerService->createCoupon($data, $request->affiliate_id);

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully.',
            'data' => $coupon,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate ([
            'name' => 'string|max:20'
        ]);

        try {
            $updatedStripeCoupon = $this->couponManagerService->updateCoupon($id, $request->name);
        } catch (CouponNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found or doesn\'t belong to user.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully (in Stripe API).',
            'data' => $updatedStripeCoupon,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        try{
            $this->couponManagerService->deleteCoupon($id);
        } catch (CouponNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found or doesn\'t belong to user.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
            'data' => ['id' => $id],
        ]);

        }
    }