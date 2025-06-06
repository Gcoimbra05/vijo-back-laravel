<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->boolean('is_private')->default(0)->after('type');
        });
    }

    public function down()
    {
        // Remove o campo is_private e volta o campo type para os valores anteriores
        Schema::table('video_requests', function (Blueprint $table) {
            $table->dropColumn('is_private');
        });
    }
};