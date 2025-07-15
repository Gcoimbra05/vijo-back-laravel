<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cred_score', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('catalog_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('cascade');
        });

        Schema::create('cred_score_kpi', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('cred_score_id')->references('id')->on('cred_score')->onDelete('cascade');
        });

        Schema::create('kpi_metric_specifications', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('kpi_id')->references('id')->on('cred_score_kpi')->onDelete('cascade');
            $table->string('name');
            $table->text('question');
            $table->float('range');
            $table->float(column: 'significance');
        });

        Schema::create('kpi_metric_values', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('kpi_metric_spec_id')->references('id')->on('kpi_metric_specifications')->onDelete('cascade');

            $table->unsignedInteger('request_id');
            $table->foreign('request_id')->references('id')->on('video_requests')->onDelete('cascade');
            $table->float('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('cred_score');
        Schema::dropIfExists('cred_score_kpi');
        Schema::dropIfExists('kpi_metric_specifications');
        Schema::dropIfExists('kpi_metric_values');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
