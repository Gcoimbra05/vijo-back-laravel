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
        DB::table('emlo_response_param_specs')
            ->where('id', 15)
            ->update([
                'simplified_param_name' => 'Self Honesty'
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
