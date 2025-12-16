<?php

use App\Models\MembershipPlan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            MembershipPlan::insert([
                'name' => 'Free',
                'slug' => 'free',
                'payment_mode' => 0,
                'monthly_cost' => 0,
                'annual_cost' => 0,
                'general_user_credits' => 200,
                'ai_user_credits' => 50
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
