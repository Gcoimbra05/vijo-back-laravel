<?php

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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->date('next_billing_date')->nullable()->comment('Next billing cycle date');
            $table->unsignedInteger('previous_plan_id')->nullable()->comment('Plan before change');
            $table->unsignedInteger('scheduled_plan_change_id')->nullable()->comment('Scheduled plan for next cycle');
            
            $table->foreign('previous_plan_id')->references('id')->on('membership_plans')->onDelete('set null');
            $table->foreign('scheduled_plan_change_id')->references('id')->on('membership_plans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['previous_plan_id']);
            $table->dropForeign(['scheduled_plan_change_id']);
            $table->dropColumn(['next_billing_date', 'previous_plan_id', 'scheduled_plan_change_id']);
        });
    }
};
