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
        Schema::create('cred_score_values', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('cred_score');
            $table->integer('measured_score');
            $table->integer('percieved score');

            $table->unsignedInteger('request_id');
            $table->foreign('request_id')->references('id')->on('video_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cred_score_values');
    }
};
