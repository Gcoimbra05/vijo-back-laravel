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
        Schema::table('realtime_sessions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'active', 'completed', 'failed'])
                ->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realtime_sessions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
