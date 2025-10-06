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
        // Example updates with WHERE clauses
        DB::table('emlo_response_param_specs')
            ->where('param_name', 'self_honesty')
            ->update(['secondary' => true]);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'clStress')
            ->update(['secondary' => true]);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'Aggression')
            ->update(['secondary' => true]);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'overallCognitiveActivity')
            ->update(['secondary' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
