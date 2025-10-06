<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponService {

    public function createCoupon($affiliateId, $stripeCouponId)
    {
        $coupon = Coupon::create([
            'affiliate_id' => $affiliateId,
            'stripe_coupon_id' => $stripeCouponId
        ]);

        return $coupon;
    }
}