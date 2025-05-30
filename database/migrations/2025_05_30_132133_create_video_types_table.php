<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('video_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->integer('kpi_no')->default(0);
            $table->integer('metric_no')->default(0);
            $table->integer('video_no')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0: Deactived, 1: Active, 2: Deleted, 3: Archieved');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        DB::table('video_types')->insert([
            [
                'name' => '0M 1KPI 1V',
                'kpi_no' => 1,
                'metric_no' => 0,
                'video_no' => 1,
                'status' => 1,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => '0M 3KPI 3V',
                'kpi_no' => 3,
                'metric_no' => 0,
                'video_no' => 3,
                'status' => 0,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => '1M 1KPI 1V',
                'kpi_no' => 1,
                'metric_no' => 1,
                'video_no' => 1,
                'status' => 1,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => '3M 3KPI 3V',
                'kpi_no' => 3,
                'metric_no' => 3,
                'video_no' => 3,
                'status' => 0,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => '9M 3KPI 3V',
                'kpi_no' => 3,
                'metric_no' => 9,
                'video_no' => 3,
                'status' => 0,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => 'Record Yourself',
                'kpi_no' => 1,
                'metric_no' => 0,
                'video_no' => 1,
                'status' => 0,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => 'Record Your Screen',
                'kpi_no' => 1,
                'metric_no' => 0,
                'video_no' => 1,
                'status' => 0,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => 'Upload a Picture and Record',
                'kpi_no' => 1,
                'metric_no' => 0,
                'video_no' => 1,
                'status' => 1,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => 'Upload a Video and Record',
                'kpi_no' => 1,
                'metric_no' => 0,
                'video_no' => 1,
                'status' => 0,
                'created_at' => '2024-05-23 13:34:23',
                'updated_at' => '2024-05-23 13:34:23',
            ],
            [
                'name' => '3M 1KPI 1V',
                'kpi_no' => 1,
                'metric_no' => 3,
                'video_no' => 1,
                'status' => 0,
                'created_at' => '2024-09-11 09:59:00',
                'updated_at' => '2024-09-11 09:59:00',
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('video_types');
    }
};