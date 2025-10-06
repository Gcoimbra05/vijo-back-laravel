<?php

use App\Models\Catalog;
use App\Models\KpiMetricSpecification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        KpiMetricSpecification::insert([

            [
                'id' => 1,
                'kpi_id' => 1,
                'video_question' => 'Describe the tasks you need to complete and when.'
            ],

            [
                'id' => 2,
                'kpi_id' => 2,
                'video_question' => 'Describe a family memory and what stands out.'
            ],

            [
                'id' => 3,
                'kpi_id' => 3,
                'video_question' => 'Describe a tradition and what meaning it brings.'
            ],

            [
                'id' => 4,
                'kpi_id' => 4,
                'video_question' => 'What story does this photo tell and how does it make you feel?'
            ],

            [
                'id' => 5,
                'kpi_id' => 5,
                'video_question' => 'What’s one favorite thing that brought you joy today, and why?'
            ],

            [
                'id' => 6,
                'kpi_id' => 6,
                'video_question' => 'Describe your recent health experience or event.'
            ],

            [
                'id' => 7,
                'kpi_id' => 7,
                'video_question' => 'Describe a recent life event and what it meant.'
            ],

            [
                'id' => 8,
                'kpi_id' => 8,
                'video_question' => 'What’s the best wisdom you’ve learned and want to share?'
            ],

            [
                'id' => 9,
                'kpi_id' => 9,
                'video_question' => 'Express a spiritual moment that shaped you.'
            ],

            [
                'id' => 10,
                'kpi_id' => 10,
                'video_question' => 'Describe your idea and why it excites you.'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
