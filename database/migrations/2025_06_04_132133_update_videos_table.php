<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('video_name', 255)->nullable()->change();
            $table->text('video_url')->nullable()->change();
            $table->integer('video_duration')->nullable()->change();
            $table->string('thumbnail_name', 255)->nullable()->change();
            $table->text('thumbnail_url')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('video_name', 255)->nullable(false)->change();
            $table->text('video_url')->nullable(false)->change();
            $table->integer('video_duration')->nullable(false)->change();
            $table->string('thumbnail_name', 255)->nullable(false)->change();
            $table->text('thumbnail_url')->nullable(false)->change();
        });
    }
};