<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('Starting affiliates table recreation migration');
        
        // Step 1: Drop existing table
        Log::info('Step 1: Dropping existing affiliates table');
        Schema::dropIfExists('affiliates');
        Log::info('✓ Affiliates table dropped successfully');

        // Step 2: Recreate table with all columns
        Log::info('Step 2: Creating new affiliates table with complete structure');
        Schema::create('affiliates', function (Blueprint $table) {
            // Original columns
            $table->integer('id', true, true);
            $table->unsignedInteger('user_id');
            $table->string('status', 50)->nullable();
            $table->timestamps();
            
            // Additional columns
            $table->string('type', 50);
            $table->unsignedInteger('creator_id');
        });
        Log::info('✓ Affiliates table structure created successfully');

        // Step 3: Add foreign key constraints
        Log::info('Step 3: Adding foreign key constraints');
        Schema::table('affiliates', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users');
        });
        Log::info('✓ Foreign key constraints added successfully');
        
        Log::info('✅ Affiliates table recreation migration completed successfully');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Log::info('Reversing affiliates table recreation migration');
        
        Log::info('Dropping recreated affiliates table');
        Schema::dropIfExists('affiliates');
        Log::info('✓ Affiliates table dropped');
        
        // Note: This migration cannot fully reverse since we're dropping and recreating
        // Any existing data will be lost. Consider backing up data before running this migration.
        Log::warning('⚠️  Note: This migration cannot restore previous data. Ensure you have backups if needed.');
        
        Log::info('✅ Migration rollback completed');
    }
};