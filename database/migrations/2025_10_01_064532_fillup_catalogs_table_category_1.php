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
                'id' => 1,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Check List',
                'message' => 'Lists keep your life and values in focus.',
                'description' => 'Make a list of what\'s important.',
                'emoji' => 'U+2705'
            ],

            [
                'id' => 2,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Family Story',
                'message' => 'Every story builds connection and belonging.',
                'description' => 'Capture and share a meaningful memory.',
                'emoji' => 'U+1F468 U+200D U+1F469 U+200D U+1F467 U+200D U+1F466'
            ],

            [
                'id' => 3,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Family Traditions',
                'message' => 'Traditions build roots across generations.',
                'description' => 'Recall a meaningful tradition.',
                'emoji' => 'U+1F389'
            ],

            [
                'id' => 4,
                'category_id' => 1,
                'video_type_id' => 8,
                'title' => 'Photo Story',
                'message' => 'Photos reconnect us to meaning and memory.',
                'description' => 'Relive a moment with a meaningful photo.',
                'emoji' => 'U+1F4F8'
            ],

            [
                'id' => 5,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Favorite Things',
                'message' => 'Noticing what you love lifts every day.',
                'description' => 'Reflect on something that brings joy.',
                'emoji' => 'U+1F60D'
            ],

            [
                'id' => 6,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Health Record',
                'message' => 'Every step for health is progress.',
                'description' => 'Summarize a health experience.',
                'emoji' => 'U+1FA7A'
            ],

            [
                'id' => 7,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Big Moment',
                'message' => 'Marking milestones fuels courage for tomorrow.',
                'description' => 'Highlight a major milestone.',
                'emoji' => 'U+1F3C6'
            ],

            [
                'id' => 8,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Wisdom Share',
                'message' => 'Sharing wisdom helps everyone learn and grow.',
                'description' => 'Pass along a life lesson.',
                'emoji' => 'U+1F9E0'
            ],

            [
                'id' => 9,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Spiritual Journey',
                'message' => 'Each spiritual step adds clarity and peace.',
                'description' => 'Reflect on a spiritual shift.',
                'emoji' => 'U+1F54A'
            ],

            [
                'id' => 10,
                'category_id' => 1,
                'video_type_id' => 1,
                'title' => 'Crazy Ideas',
                'message' => 'Bold ideas shape a more creative future.',
                'description' => 'Share a creative or bold idea.',
                'emoji' => 'U+1F4A1'
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
