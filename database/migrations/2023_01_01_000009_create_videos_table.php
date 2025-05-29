<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('request_id');
            $table->string('video_name', 255)->nullable(false);
            $table->text('video_url')->nullable(false);
            $table->integer('video_duration')->nullable(false);
            $table->string('thumbnail_name', 255)->nullable(false);
            $table->text('thumbnail_url')->nullable(false);
            $table->timestamps();

            // Define foreign key
            $table->foreign('request_id')->references('id')->on('video_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}