<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuleConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insertData = [
            [
                'rule_id' => 1,
                'condition' => json_encode([
                    "type" => "compound",
                    "operator" => "AND", 
                    "conditions" => [
                        ["param" => "overallCognitiveActivity", "operator" => ">", "value" => 38], 
                        ["param" => "imagination", "operator" => "<", "value" => 5]
                    ]
                ]),
                'message' => 'overallCognitiveActivity is in correlation with imagination',
                'order_index' => 1,
                'active' => true,
            ],
            [
                'rule_id' => 1,
                'condition' => json_encode([
                    "type" => "simple",
                    "param" => "overallCognitiveActivity",
                    "operator" => ">", 
                    "value" => 100
                ]),
                'message' => 'overallCognitiveActivity is higher than normal.',
                'order_index' => 2,  // Changed to 2
                'active' => true,
            ],
        ];

        DB::table('rule_conditions')->insertOrIgnore($insertData);
    }
}