<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rules')->insert([

            'name' =>'overallCognitiveActivity value rating',
            'param_name' => 'overallCognitiveActivity',
            'active' => true


        ]);
    }
}
