<?php

use App\Models\Catalog;
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
        Catalog::insert([
            [
                'id' => 11,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Stress Manager',
                'message' => 'Every time you manage stress, you practice resilience.',
                'description' => 'Reflect and manage stress.',
                'emoji' => 'U+1F60C'
            ],

            [
                'id' => 12,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Mood Monitor',
                'message' => 'Attention to mood helps you steer your day.',
                'description' => 'Observe and track your mood.',
                'emoji' => 'U+1F4C8'
            ],

            [
                'id' => 13,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Night Notes',
                'message' => 'Releasing your thoughts before bed is a step toward healing rest.',
                'description' => 'Prepare for a restful sleep by releasing thoughts.',
                'emoji' => 'U+1F319'
            ],

            [
                'id' => 14,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'EQ Pulse',
                'message' => 'You’re strengthening your EQ with every reflection you record.',
                'description' => 'Spot triggers, manage responses, and empathize with others.',
                'emoji' => 'U+1F9E0'
            ],

            [
                'id' => 15,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Three Words',
                'message' => 'Naming emotions gives power to change.',
                'description' => 'Identify three words describing how you feel.',
                'emoji' => 'U+2753'
            ],

            [
                'id' => 16,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Anger Reset',
                'message' => 'Letting go of anger gives you back your energy.',
                'description' => 'Notice, process, and let go of anger.',
                'emoji' => 'U+1F624'
            ],

            [
                'id' => 17,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Social Mirror',
                'message' => 'Relationships show us our strengths and growth.',
                'description' => 'Reflect on social interactions.',
                'emoji' => 'U+1FA9E'
            ],

            [
                'id' => 18,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Empathy Practice',
                'message' => 'Empathy brings trust and healing.',
                'description' => 'Practice empathy and understanding.',
                'emoji' => 'U+1F91D'
            ],

            [
                'id' => 19,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Confidence Boost',
                'message' => 'Recall a confident moment or win.',
                'description' => 'Every confident step builds your path forward.',
                'emoji' => 'U+1F4AA'
            ],

            [
                'id' => 20,
                'category_id' => 2,
                'video_type_id' => 3,
                'title' => 'Gratitude Glow',
                'message' => 'Gratitude shines through in all you do.',
                'description' => 'Savor something you appreciate today.',
                'emoji' => 'U+1F64F'
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
