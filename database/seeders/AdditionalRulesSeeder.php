<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AdditionalRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insertData = [
            [
            'name' =>'EDP-Confident value rating',
            'param_name' => 'EDP-Confident',
            'simplified_param_name' => 'Confidence',
            'active' => true
            ],
            [
            'name' =>'EDP-Passionate value rating',
            'param_name' => 'EDP-Passionate',
            'simplified_param_name' => 'Emotional Drive',
            'active' => true
            ],
            [
            'name' =>'EDP-Energetic value rating',
            'param_name' => 'EDP-Energetic',
            'simplified_param_name' => 'Energy boost',
            'active' => true
            ],
            [
            'name' =>'EDP-Concentrated value rating',
            'param_name' => 'EDP-Concentrated',
            'simplified_param_name' => 'Focus Level',
            'active' => true
            ],
            [
            'name' =>'EDP-Thoughtful value rating',
            'param_name' => 'EDP-Thoughtful',
            'simplified_param_name' => 'Mental Depth',
            'active' => true
            ],
            [
            'name' =>'EDP-Hesitation value rating',
            'param_name' => 'EDP-Hesitation',
            'simplified_param_name' => 'Pause Signal',
            'active' => true
            ],
            [
            'name' =>'EDP-Stressful value rating',
            'param_name' => 'EDP-Stressful',
            'simplified_param_name' => 'Stress Level',
            'active' => true
            ],
            [
            'name' =>'EDP-Uneasy value rating',
            'param_name' => 'EDP-Uneasy',
            'simplified_param_name' => 'Uneasy',
            'active' => true
            ],



            [
            'name' =>'clStress value rating',
            'param_name' => 'clStress',
            'simplified_param_name' => 'Stress Recovery',
            'active' => true
            ],
        ];

        DB::table('rules')->insertOrIgnore($insertData);
    }
}
