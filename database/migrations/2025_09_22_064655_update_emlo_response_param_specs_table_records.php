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
    public function up()
    {
        // Example updates with WHERE clauses
        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Anticipation')
            ->update(['short_description' => 'Excitement or nervousness ahead']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Concentrated')
            ->update(['short_description' => 'Deep focus and engagement level']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Confident')
            ->update(['short_description' => 'Steadiness and certainty level']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Emotional')
            ->update(['short_description' => 'Feeling expression level']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Energetic')
            ->update(['short_description' => 'Alertness or fatigue level']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Hesitation')
            ->update(['short_description' => 'Uncertainty or caution signals']);


        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Passionate')
            ->update(['short_description' => 'Intensity and personal interest']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Stressful')
            ->update(['short_description' => 'Pressure and overwhelm signals']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Thoughtful')
            ->update(['short_description' => 'Careful word choice level']);


        DB::table('emlo_response_param_specs')
            ->where('param_name', 'EDP-Uneasy')
            ->update(['short_description' => 'Discomfort or unease signals']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'Aggression')
            ->update(['short_description' => 'Anger intensity in voice']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'self_honesty')
            ->update(['short_description' => 'Openness versus holding back']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'overallCognitiveActivity')
            ->update(['short_description' => 'Thought-emotion alignment']);

        DB::table('emlo_response_param_specs')
            ->where('param_name', 'clStress')
            ->update(['short_description' => 'Returning to calm after stress']);
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
