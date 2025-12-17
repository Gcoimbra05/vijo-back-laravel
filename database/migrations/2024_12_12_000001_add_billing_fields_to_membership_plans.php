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
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->integer('max_recordings')->nullable()->comment('Max number of recordings per month');
            $table->integer('max_storage_vijos')->nullable()->comment('Max number of stored vijos');
            $table->integer('storage_mb')->nullable()->comment('Total storage in MB');
            $table->boolean('has_ai_personalized_plans')->default(false);
            $table->boolean('has_full_ai_access')->default(false);
            $table->boolean('has_exports')->default(false);
            $table->boolean('is_free')->default(false);
            $table->integer('display_order')->default(0)->comment('Order for display in UI');
            $table->string('badge_text', 50)->nullable()->comment('Badge text: Popular, Recommended, etc');
            $table->boolean('is_recommended')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn([
                'max_recordings',
                'max_storage_vijos',
                'storage_mb',
                'has_ai_personalized_plans',
                'has_full_ai_access',
                'has_exports',
                'is_free',
                'display_order',
                'badge_text',
                'is_recommended'
            ]);
        });
    }
};
