<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;
use Illuminate\Support\Str;

class MembershipPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Spark',
                'slug' => 'spark',
                'description' => 'Perfect for getting started with Vijo',
                'payment_mode' => 2, // Recurring
                'monthly_cost' => 0.00,
                'annual_cost' => 0.00,
                'payment_link' => null,
                'price_id' => null,
                'status' => 1,
                'general_user_credits' => 10,
                'ai_user_credits' => 5,
                'max_recordings' => 10,
                'max_storage_vijos' => 20,
                'storage_mb' => 500, // 500 MB
                'has_ai_personalized_plans' => false,
                'has_full_ai_access' => false,
                'has_exports' => false,
                'is_free' => true,
                'display_order' => 1,
                'badge_text' => 'Free',
                'is_recommended' => false
            ],
            [
                'name' => 'Seeker',
                'slug' => 'seeker',
                'description' => 'For individuals seeking consistent growth',
                'payment_mode' => 2,
                'monthly_cost' => 9.99,
                'annual_cost' => 99.99,
                'payment_link' => 'https://buy.stripe.com/test_seeker', // TODO: Replace with actual
                'price_id' => 'price_seeker_monthly', // TODO: Replace with actual
                'status' => 1,
                'general_user_credits' => 50,
                'ai_user_credits' => 25,
                'max_recordings' => 100,
                'max_storage_vijos' => 200,
                'storage_mb' => 2048, // 2 GB
                'has_ai_personalized_plans' => true,
                'has_full_ai_access' => false,
                'has_exports' => true,
                'is_free' => false,
                'display_order' => 2,
                'badge_text' => 'Popular',
                'is_recommended' => true
            ],
            [
                'name' => 'Voyager',
                'slug' => 'voyager',
                'description' => 'For professionals on a transformative journey',
                'payment_mode' => 2,
                'monthly_cost' => 49.99,
                'annual_cost' => 499.99,
                'payment_link' => 'https://buy.stripe.com/test_voyager', // TODO: Replace with actual
                'price_id' => 'price_voyager_monthly', // TODO: Replace with actual
                'status' => 1,
                'general_user_credits' => 200,
                'ai_user_credits' => 100,
                'max_recordings' => null, // Unlimited
                'max_storage_vijos' => 1000,
                'storage_mb' => 10240, // 10 GB
                'has_ai_personalized_plans' => true,
                'has_full_ai_access' => true,
                'has_exports' => true,
                'is_free' => false,
                'display_order' => 3,
                'badge_text' => null,
                'is_recommended' => false
            ],
            [
                'name' => 'Luminary',
                'slug' => 'luminary',
                'description' => 'For leaders and teams requiring enterprise solutions',
                'payment_mode' => 2,
                'monthly_cost' => 99.99,
                'annual_cost' => 999.99,
                'payment_link' => 'https://buy.stripe.com/test_luminary', // TODO: Replace with actual
                'price_id' => 'price_luminary_monthly', // TODO: Replace with actual
                'status' => 1,
                'general_user_credits' => 500,
                'ai_user_credits' => 250,
                'max_recordings' => null, // Unlimited
                'max_storage_vijos' => null, // Unlimited
                'storage_mb' => 51200, // 50 GB
                'has_ai_personalized_plans' => true,
                'has_full_ai_access' => true,
                'has_exports' => true,
                'is_free' => false,
                'display_order' => 4,
                'badge_text' => 'Enterprise',
                'is_recommended' => false
            ]
        ];

        foreach ($plans as $planData) {
            MembershipPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $this->command->info('Membership plans seeded successfully!');
    }
}
