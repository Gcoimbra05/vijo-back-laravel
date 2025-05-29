<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalog_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('catalog_id')->default(0);
            $table->tinyInteger('reference_type')->default(0);
            $table->string('metric1_title', 255)->nullable();
            $table->string('metric1_question', 255)->nullable();
            $table->string('metric1_question_option1', 50)->nullable();
            $table->string('metric1_question_option2', 50)->nullable();
            $table->integer('metric1_question_option1val')->default(0);
            $table->integer('metric1_question_option2val')->default(0);
            $table->integer('metric1_question_label')->default(0);
            $table->tinyInteger('metric1_significance')->default(0);
            $table->string('metric2_title', 255)->nullable();
            $table->string('metric2_question', 255)->nullable();
            $table->string('metric2_question_option1', 50)->nullable();
            $table->string('metric2_question_option2', 50)->nullable();
            $table->integer('metric2_question_option1val')->default(0);
            $table->integer('metric2_question_option2val')->default(0);
            $table->integer('metric2_question_label')->default(0);
            $table->tinyInteger('metric2_significance')->default(0);
            $table->string('metric3_title', 255)->nullable();
            $table->string('metric3_question', 255)->nullable();
            $table->string('metric3_question_option1', 50)->nullable();
            $table->string('metric3_question_option2', 50)->nullable();
            $table->integer('metric3_question_option1val')->default(0);
            $table->integer('metric3_question_option2val')->default(0);
            $table->integer('metric3_question_label')->default(0);
            $table->tinyInteger('metric3_significance')->default(0);
            $table->string('video_question', 255)->nullable();
            $table->tinyInteger('metric4_significance')->default(0);
            $table->tinyInteger('metric5_significance')->default(0);
            $table->tinyInteger('status')->default(1)->comment('0: Deactived, 1: Active, 2: Deleted, 3: Archieved');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catalog_questions');
    }
};