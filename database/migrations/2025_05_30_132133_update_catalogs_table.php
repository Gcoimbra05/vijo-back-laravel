<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->unsignedInteger('video_type_id')->nullable()->after('is_premium');
            $table->boolean('is_multipart')->default(0)->after('video_type_id');

            $table->foreign('video_type_id')->references('id')->on('video_types')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropForeign(['video_type_id']);
            $table->dropColumn(['video_type_id', 'is_multipart']);
        });
    }
};