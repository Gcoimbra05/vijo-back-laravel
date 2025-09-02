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
        Schema::create('emlo_insights_secondary_metrics', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('emlo_param_spec_id');
            $table->foreign('emlo_param_spec_id')->references('id')->on('emlo_response_param_specs')->onDelete('cascade');
            
            $table->unsignedInteger('request_id');
            $table->foreign('request_id')->references('id')->on('video_requests');
            
            $table->text('info_array');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emlo_insights_secondary_metrics');
    }
};
