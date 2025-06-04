<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EmloResponseParamSpecsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insertData = [
            [

            'param_name' => 'overallCognitiveActivity',
            'description' => 'Overall Cognitive Activity (OCA) reflects the combined magnitude of emotional and cognitive activity within the brain. It is derived from analyzing physiological signals, such as EEG data, to assess the level of mental engagement. This metric helps in understanding how intensely an individual is processing information and emotions simultaneously.',
            'min' => 0,
            'max' => 1500,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),

            ],

            [

            'param_name' => 'imagination',
            'description' => 'Imagination reflects the ability to think up scenarious in your mental headspace.',
            'min' => 0,
            'max' => 500,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),

            ],
    
    ];

    DB::table('emlo_response_param_specs')->insertOrIgnore($insertData);
    }
}
