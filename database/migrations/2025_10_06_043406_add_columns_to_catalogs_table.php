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
        Schema::table('catalogs', function (Blueprint $table) {
            $table->text('primary_modality')->nullable();
            $table->text('secondary_modality')->nullable();
            $table->text('tertiary_modality')->nullable();
            $table->text('best_time_of_day_to_record')->nullable();
            $table->text('frequency_of_recording')->nullable();
            $table->text('frequency_of_reviewing')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn('primary_modality');
            $table->dropColumn('secondary_modality');
            $table->dropColumn('tertiary_modality');
            $table->dropColumn('best_time_of_day_to_record');
            $table->dropColumn('frequency_of_recording');
            $table->dropColumn('frequency_of_reviewing');
        });
    }
};
