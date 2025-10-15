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
        DB::table('categories')
            ->where('id', 1)
            ->update([
                'emoji' => 'U+1F4DC'
            ]);

        DB::table('categories')
            ->where('id', 2)
            ->update([
                'emoji' => 'U+1F9D8'
            ]);

        DB::table('categories')
            ->where('id', 3)
            ->update([
                'emoji' => 'U+1F680'
            ]);
    }


};
