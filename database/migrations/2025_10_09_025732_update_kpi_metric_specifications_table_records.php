<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('kpi_metric_specifications')
            ->where('id', 3)
            ->update([
                'video_question' => 'Dear Vijo, today I want to remember …'
            ]);

        DB::table('kpi_metric_specifications')
            ->where('id', 11)
            ->update([
                'video_question' => 'What triggered your stress today and how did you manage your response?',
                'question' => 'I recognized my stress and managed it today.'
            ]);

        DB::table('kpi_metric_specifications')
            ->where('id', 13)
            ->update([
                'video_question' => 'What’s on your mind keeping you up, and how can you let it go tonight?',
                'question' => 'I cleared my mind and I\'m ready for a good night\'s sleep.'
            ]);

        DB::table('kpi_metric_specifications')
            ->where('id', 29)
            ->update([
                'video_question' => 'What is a big dream you\'d love to make real, and why is it meaningful?',
                'question' => 'I dreamed big today and took one step toward it.'
            ]);



    }
};
