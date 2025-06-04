<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catalog_metric_question_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->nullable();
            $table->string('metricOption1Emoji', 100)->nullable();
            $table->string('metricOption1Text', 100)->nullable();
            $table->string('metricOption3Emoji', 100)->nullable();
            $table->string('metricOption3Text', 100)->nullable();
            $table->string('metricOption5Emoji', 100)->nullable();
            $table->string('metricOption5Text', 100)->nullable();
            $table->string('metricOption7Emoji', 100)->nullable();
            $table->string('metricOption7Text', 100)->nullable();
            $table->string('metricOption9Emoji', 100)->nullable();
            $table->string('metricOption9Text', 100)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: Deactived, 1: Active, 2: Deleted, 3: Archieved');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        // Insert initial data
        DB::table('catalog_metric_question_labels')->insert([
            [
                'title' => 'Unhappy to happy',
                'metricOption1Emoji' => 'U+1F625',
                'metricOption1Text' => 'I feel Discontented',
                'metricOption3Emoji' => 'U+1F614',
                'metricOption3Text' => 'I feel Unsatisfied',
                'metricOption5Emoji' => 'U+1F610',
                'metricOption5Text' => 'I feel Neutral',
                'metricOption7Emoji' => 'U+1F60A',
                'metricOption7Text' => 'I feel Content',
                'metricOption9Emoji' => 'U+1F604',
                'metricOption9Text' => 'I feel Joyful',
                'status' => 1,
                'created_at' => '2024-10-04 06:08:27',
                'updated_at' => '2024-10-04 06:08:27',
            ],
            [
                'title' => 'Agree to Disagree ',
                'metricOption1Emoji' => 'U+1F621',
                'metricOption1Text' => 'I Strongly Disagree ',
                'metricOption3Emoji' => 'U+1F616',
                'metricOption3Text' => 'I Disagree ',
                'metricOption5Emoji' => 'U+1F615',
                'metricOption5Text' => 'I am Undecided ',
                'metricOption7Emoji' => 'U+1F60A',
                'metricOption7Text' => 'I Agree ',
                'metricOption9Emoji' => 'U+1F61C',
                'metricOption9Text' => 'I Strongly Agree ',
                'status' => 1,
                'created_at' => '2024-10-09 04:34:31',
                'updated_at' => '2024-10-09 04:34:31',
            ],
            [
                'title' => 'Feelings',
                'metricOption1Emoji' => 'U+1F615',
                'metricOption1Text' => 'I feel Confused',
                'metricOption3Emoji' => 'U+1F64F',
                'metricOption3Text' => 'I feel Grateful',
                'metricOption5Emoji' => 'U+1F60A',
                'metricOption5Text' => 'I feel Content',
                'metricOption7Emoji' => 'U+1F624',
                'metricOption7Text' => 'I feel Frustrated',
                'metricOption9Emoji' => 'U+1F62F',
                'metricOption9Text' => 'I feel Amazed',
                'status' => 1,
                'created_at' => '2024-10-09 04:45:48',
                'updated_at' => '2024-10-09 04:45:48',
            ],
            [
                'title' => 'Knowledge of Event ',
                'metricOption1Emoji' => 'U+1F910',
                'metricOption1Text' => 'No Knowledge',
                'metricOption3Emoji' => 'U+1F626',
                'metricOption3Text' => 'Minimal Knowledge',
                'metricOption5Emoji' => 'U+1F610',
                'metricOption5Text' => 'Limited Knowledge',
                'metricOption7Emoji' => 'U+1F60A',
                'metricOption7Text' => 'Moderate Knowledge',
                'metricOption9Emoji' => 'U+1F604',
                'metricOption9Text' => 'Full Knowledge',
                'status' => 1,
                'created_at' => '2024-12-01 00:34:37',
                'updated_at' => '2024-12-01 00:34:37',
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('catalog_metric_question_labels');
    }
};