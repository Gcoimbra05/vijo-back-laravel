<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->unsignedInteger('llm_template_id')->nullable();
            $table->foreign('llm_template_id')->references('id')->on('llm_templates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->dropColumn('llm_template_id');
        });
    }
};
