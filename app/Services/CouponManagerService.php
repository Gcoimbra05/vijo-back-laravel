<?php

namespace App\Services;

use App\Exceptions\CouponNotFoundException;
use App\Exceptions\StripeCouponNotFoundException;
use App\Models\Coupon;



class CouponManagerService {
    public function __construct(
        protected CouponService $couponService,
        protected StripeCouponService $stripeCouponService
    ){}

    public function getAllForAffiliate($affiliate, $userId)
    {
        $coupons = Coupon::select()->get();

        $completeCoupons = [];

        foreach ($coupons as $coupon) {
            $stripeCoupon = $this->stripeCouponService->getStripeCoupon($coupon->stripe_coupon_id);
            if ($stripeCoupon) {
                $completeCoupon = array_merge(
                    $coupon->toArray(),
                    ['stripe' => $stripeCoupon]
                );

                $completeCoupons [] = $completeCoupon;

            } else {
                $completeCoupon = array_merge(
                    $coupon->toArray(),
                    ['stripe' => null]
                );
            }
        }
        return $completeCoupons;
    }

    public function getSingleForAffiliate($id, $userId, $affiliate)
    {
        $coupon = Coupon::find($id);

        if (!$coupon || $affiliate?->user_id !== $userId) {
            throw new CouponNotFoundException ('Coupon not found or doesn\'t belong to user.');
        }

        $stripeCoupon = $this->stripeCouponService->getStripeCoupon($coupon->stripe_coupon_id);
        if ($stripeCoupon) {
            $completeCoupon = array_merge(
                $coupon->toArray(),
                ['stripe' => $stripeCoupon]
            );

            return $completeCoupon;

        } else {
            throw new StripeCouponNotFoundException ('Stripe Coupon not found.');
        }
    }

    public function createCoupon(array $data, $affiliateId)
    {
        $stripeCoupon = $this->stripeCouponService->createStripeCoupon($data);
        $coupon = $this->couponService->createCoupon($affiliateId, $stripeCoupon->id);
        return $coupon;
    }

    public function updateCoupon($id, $name)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            throw new CouponNotFoundException ('Coupon not found or doesn\'t belong to user.');
        }

        $updatedStripeCoupon = $this->stripeCouponService->updateStripeCoupon($name, $coupon->stripe_coupon_id);
        return $updatedStripeCoupon;
    }

    public function deleteCoupon($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            throw new CouponNotFoundException ('Coupon not found or doesn\'t belong to user.');
        }

        $coupon->delete();

        $deletedCoupon = $this->stripeCouponService->deleteStripeCoupon($coupon->stripe_coupon_id);
        return $deletedCoupon;

    
    }
}