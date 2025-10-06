<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('catalogs')
            ->where('id', 6)
            ->update(['video_type_id' => 8]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('id', 6)
            ->update(['id' => 1]);
    }
};

