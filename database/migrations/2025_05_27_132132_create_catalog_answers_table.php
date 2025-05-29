<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalog_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('catalog_id');
            $table->unsignedInteger('request_id');
            $table->decimal('cred_score', 10, 2)->default(0.00);
            $table->string('metric1_answer', 50)->default('0');
            $table->decimal('metric1Range', 10, 2)->default(0.00);
            $table->tinyInteger('metric1Significance')->default(0);
            $table->string('metric2_answer', 50)->default('0');
            $table->decimal('metric2Range', 10, 2)->default(0.00);
            $table->tinyInteger('metric2Significance')->default(0);
            $table->string('metric3_answer', 50)->default('0');
            $table->decimal('metric3Range', 10, 2)->default(0.00);
            $table->tinyInteger('metric3Significance')->default(0);
            $table->string('n8n_executionId', 50)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('cascade');
            $table->foreign('request_id')->references('id')->on('video_requests')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalog_answers');
    }
};