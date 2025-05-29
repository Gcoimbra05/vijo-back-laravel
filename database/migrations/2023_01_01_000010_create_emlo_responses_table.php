<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmloResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('emlo_responses', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('request_id');
            $table->json('raw_response')->nullable(false);
            $table->timestamps();

            // Define foreign key
            $table->foreign('request_id')->references('id')->on('video_requests')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('emlo_responses');
    }
}