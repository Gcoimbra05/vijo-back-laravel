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
        DB::table('catalogs')
            ->where('id', 3)
            ->update([
                'title' => 'Dear Vijo',
                'message' => 'Every message plants a seed for the future.',
                'description' => 'Record and remember whats on your mind.'
            ]);

        DB::table('catalogs')
            ->where('id', 11)
            ->update([
                'title' => 'Stress Manager',
                'message' => 'Every time you manage stress, you practice resilience.',
                'description' => 'Reflect and manage stress.'
            ]);

        DB::table('catalogs')
            ->where('id', 13)
            ->update([
                'title' => 'Night Notes',
                'message' => 'Releasing your thoughts before bed is a step toward healing rest.',
                'description' => 'Prepare for a restful sleep by releasing thoughts.'
            ]);

        DB::table('catalogs')
            ->where('id', 29)
            ->update([
                'title' => 'Dream Big',
                'message' => 'Every great achievement starts with a bold dream.',
                'description' => 'Envision bold hopes or goals that inspire you to reach higher.'
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
