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
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->text('text')->nullable();
            $table->text('text_w_segment_emotions')->nullable();

            $table->unsignedInteger('request_id');
            $table->foreign('request_id')->references('id')->on('video_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
