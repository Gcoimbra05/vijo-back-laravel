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
                'id' => 21,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Goal Getter',
                'message' => 'Every single step adds up; keep moving.',
                'description' => 'Move toward a key goal or value.',
                'emoji' => 'U+1F3AF'
            ],

            [
                'id' => 22,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Balanced Wheel',
                'message' => 'Balance work, life, and personal values daily.',
                'description' => 'Life feels better when it\'s balanced.',
                'emoji' => 'U+2696'
            ],

            [
                'id' => 23,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Habit Capture',
                'message' => 'Habits shape your future one choice at a time.',
                'description' => 'Track and refine daily habits.',
                'emoji' => 'U+1F4C6'
            ],

            [
                'id' => 24,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Energy Gift',
                'message' => 'Shared energy always finds its way back to you.',
                'description' => 'Notice how you shared your energy.',
                'emoji' => 'U+26A1'
            ],

            [
                'id' => 25,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Comfort Zone',
                'message' => 'Growth only begins where comfort ends.',
                'description' => 'Take purposeful risks beyond comfort.',
                'emoji' => 'U+1F6AA'
            ],

            [
                'id' => 26,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Fear Face',
                'message' => 'Courage grows every time you try.',
                'description' => 'Face a fear and build courage.',
                'emoji' => 'U+1F631'
            ],

            [
                'id' => 27,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Bounce Back',
                'message' => 'Resilience is getting up again‚ every time.',
                'description' => 'Be resilient after setbacks.',
                'emoji' => 'U+1F501'
            ],

            [
                'id' => 28,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Be Better',
                'message' => 'Small gains lead to big changes when added daily.',
                'description' => 'Incremental improvement every day.',
                'emoji' => 'U+2B06'
            ],

            [
                'id' => 29,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Growth Mindset',
                'message' => 'Every struggle can teach you something new.',
                'description' => 'Learn from setbacks and challenges.',
                'emoji' => 'U+1F331'
            ],

            [
                'id' => 30,
                'category_id' => 3,
                'video_type_id' => 3,
                'title' => 'Learning Moment',
                'message' => 'Every lesson is a tool for tomorrow.',
                'description' => 'Share a learning or self-discovery.',
                'emoji' => 'U+1F4DA'
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
