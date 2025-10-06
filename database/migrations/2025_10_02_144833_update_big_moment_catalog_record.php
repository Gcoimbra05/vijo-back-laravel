<?php

use App\Models\Catalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('catalogs')
            ->where('id', 7)
            ->update([
                'title' => 'Life Moments',
                'description' => 'Highlight any life events.',
                'message' => 'Marking life events fuels courage for tomorrow.'
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
