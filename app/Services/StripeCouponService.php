<?php 

namespace App\Services;

use Stripe\StripeClient;

class StripeCouponService
{
    protected $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function getAllStripeCoupons()
    {
        $coupons = $this->stripe->coupons->all();
        return $coupons;
    }

    public function getStripeCoupon($stripeCouponId)
    {
        $coupon = $this->stripe->coupons->retrieve($stripeCouponId, []);
        return $coupon;
    }

    public function createStripeCoupon(array $data)
    {
        return $this->stripe->coupons->create($data);
    }

    public function updateStripeCoupon($name, $stripeCouponId)
    {
        $updated = $this->stripe->coupons->update(
            $stripeCouponId,
            ['name' => ['order_id' => $name]]
        );

        return $updated;
    }

    public function deleteStripeCoupon($stripeCouponId)
    {
        $deleted = $this->stripe->coupons->delete($stripeCouponId, []);
        return $deleted;
    }
}
