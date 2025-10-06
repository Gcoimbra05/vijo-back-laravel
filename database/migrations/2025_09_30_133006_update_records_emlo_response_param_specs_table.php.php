<?php

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
            ->where('param_name', 'clStress')
            ->update(['needs_normalization' => 1]);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'Aggression')
            ->update(['needs_normalization' => 1]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
