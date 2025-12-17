<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\MembershipPlan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

            MembershipPlan::insert([
            [
                'name' => 'Grow',
                'slug' => 'grow',
                'payment_mode' => 1,
                'monthly_cost' => 8,
                'annual_cost' => 0,
                'general_user_credits' => 200,
                'ai_user_credits' => 50,
                'payment_link' => 'https://buy.stripe.com/test_aFaeV6e2a2HWbpff4dg7e03',
                "price_id" => 'price_1SchHC2MTNMYHGSeVuN054rp'
            ],
            [
                'name' => 'Grow',
                'slug' => 'growAnn',
                'payment_mode' => 1,
                'monthly_cost' => 0,
                'annual_cost' => 80,
                'general_user_credits' => 200,
                'ai_user_credits' => 50,
                'payment_link' => 'https://buy.stripe.com/test_4gM14g0bk2HWgJz4pzg7e04',
                "price_id" => 'price_1SchPO2MTNMYHGSecMKcCJ8N'
            ],

            [
                'name' => 'Explore',
                'slug' => 'explore',
                'payment_mode' => 1,
                'monthly_cost' => 24,
                'annual_cost' => 0,
                'general_user_credits' => 750,
                'ai_user_credits' => 750,
                'payment_link' => 'https://buy.stripe.com/test_fZubIUcY66Yc0KB3lvg7e06',
                "price_id" => 'price_1Sd2CI2MTNMYHGSeSs6wxwWi'
            ],
            [
                'name' => 'Explore',
                'slug' => 'exploreAnn',
                'payment_mode' => 1,
                'monthly_cost' => 0,
                'annual_cost' => 240,
                'general_user_credits' => 750,
                'ai_user_credits' => 750,
                'payment_link' => 'https://buy.stripe.com/test_6oU14g1fo4Q40KB2hrg7e07',
                "price_id" => 'price_1Sd2Ck2MTNMYHGSewVCuJNYY'
            ],

            [
                'name' => 'Thrive',
                'slug' => 'thrive',
                'payment_mode' => 1,
                'monthly_cost' => 48,
                'annual_cost' => 0,
                'general_user_credits' => 4000,
                'ai_user_credits' => 4000,
                'payment_link' => 'https://buy.stripe.com/test_6oU28k9LU1DSctjbS1g7e02',
                "price_id" => 'price_1Sd2EQ2MTNMYHGSebMN70DSJ'
            ],
            [
                'name' => 'Thrive',
                'slug' => 'thriveAnn',
                'payment_mode' => 1,
                'monthly_cost' => 0,
                'annual_cost' => 480,
                'general_user_credits' => 4000,
                'ai_user_credits' => 4000,
                'payment_link' => 'https://buy.stripe.com/test_00w6oAbU24Q464VcW5g7e05',
                "price_id" => 'price_1Sd2Fd2MTNMYHGSeFIpmT6Qt'
            ]
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
