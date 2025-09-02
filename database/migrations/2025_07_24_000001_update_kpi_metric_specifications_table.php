<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kpi_metric_specifications', function (Blueprint $table) {
            $table->text('video_question')->nullable()->after('question');
        });
    }

    public function down()
    {
        Schema::table('kpi_metric_specifications', function (Blueprint $table) {
            $table->dropColumn('video_question');
        });
    }
};