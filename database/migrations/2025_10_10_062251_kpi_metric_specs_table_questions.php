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
                'id' => 11,
                'kpi_id' => 11,
                'video_question' => 'What triggered your stress today and how did you manage your response?',
                'question' => 'I recognized my stress and managed it today.',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 12,
                'kpi_id' => 12,
                'video_question' => 'Which emotions shaped your mood today, and how did you respond?',
                'question' => 'I stayed aware of emotions driving my mood today.',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 13,
                'kpi_id' => 13,
                'video_question' => 'What’s on your mind keeping you up, and how can you let it go tonight?',
                'question' => 'I cleared my mind and I\'m ready for a good night\'s sleep.',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 14,
                'kpi_id' => 14,
                'question' => 'I noticed triggers and managed my actions and empathy today.',
                'video_question' => 'How did you spot triggers, manage your response, or show empathy?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 15,
                'kpi_id' => 15,
                'question' => 'I can identify and name my true emotions today.',
                'video_question' => 'Which three words describe your feelings today, and why?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 16,
                'kpi_id' => 16,
                'question' => 'I managed anger healthily today.',
                'video_question' => 'How did you manage or release anger today?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 17,
                'kpi_id' => 17,
                'question' => 'I improved a relationship today.',
                'video_question' => 'How do you think others saw you today, and how does that make you feel?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 18,
                'kpi_id' => 18,
                'question' => 'I showed empathy to someone today.',
                'video_question' => 'How did you act with empathy today?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 19,
                'kpi_id' => 19,
                'question' => 'I acted with self-confidence today.',
                'video_question' => 'How did you act with confidence and why did it matter?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 20,
                'kpi_id' => 20,
                'question' => 'I practiced gratitude today.',
                'video_question' => 'What are you grateful for today, and how did it make you feel?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 21,
                'kpi_id' => 21,
                'question' => 'I made real progress on a key goal today.',
                'video_question' => 'How did you work towards your goal, and why did it matter?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 22,
                'kpi_id' => 22,
                'question' => 'I made choices for better balance in life.',
                'video_question' => 'How did you actively balance important areas today?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 23,
                'kpi_id' => 23,
                'question' => 'I strengthened a key habit today.',
                'video_question' => 'How did you uphold or improve a habit?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 24,
                'kpi_id' => 24,
                'question' => 'I lifted or supported someone with my energy today.',
                'video_question' => 'How did you share your energy for good today?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 25,
                'kpi_id' => 25,
                'question' => 'I stretched beyond my comfort zone today.',
                'video_question' => 'Why did you go outside your comfort zone?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 26,
                'kpi_id' => 26,
                'question' => 'I made progress facing a fear today.',
                'video_question' => 'How did you face a fear and why did it matter?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 27,
                'kpi_id' => 27,
                'question' => 'I responded to a challenge with resilience today.',
                'video_question' => 'How did you bounce back after a setback?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 28,
                'kpi_id' => 28,
                'question' => 'I did at least one thing to better myself today.',
                'video_question' => 'What small thing made you better today?',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 29,
                'kpi_id' => 29,
                'video_question' => 'What is a big dream you\'d love to make real, and why is it meaningful?',
                'question' => 'I dreamed big today and took one step toward it.',
                'range' => 10,
                'significance' => 1

            ],

            [
                'id' => 30,
                'kpi_id' => 30,
                'question' => 'I learned or discovered something new today.',
                'video_question' => 'Describe what you learned today and how you\'ll use it.',
                'range' => 10,
                'significance' => 1

            ]
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
