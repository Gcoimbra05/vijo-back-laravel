<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $condition1 = [
            "type" => "compound",
            "operator" => "AND",
            "conditions" => [
                ["param" => "overallCognitiveActivity", "value" => 1, "operator" => ">"],
                ["param" => "overallCognitiveActivity", "value" => 40, "operator" => "<"],
            ],
        ];

        $condition2 = [
            "type" => "compound",
            "operator" => "AND",
            "conditions" => [
                ["param" => "overallCognitiveActivity", "value" => 41, "operator" => ">"],
                ["param" => "overallCognitiveActivity", "value" => 60, "operator" => "<"],
            ],
        ];

        $condition3 = [
            "type" => "compound",
            "operator" => "AND",
            "conditions" => [
                ["param" => "overallCognitiveActivity", "value" => 61, "operator" => ">"],
                ["param" => "overallCognitiveActivity", "value" => 80, "operator" => "<"],
            ],
        ];

        $condition4 = [
            "type" => "compound",
            "operator" => "AND",
            "conditions" => [
                ["param" => "overallCognitiveActivity", "value" => 81, "operator" => ">"],
                ["param" => "overallCognitiveActivity", "value" => 100, "operator" => "<"],
            ],
        ];

        DB::table('rule_conditions')
            ->where('id', 37)
            ->update([
                'condition' => json_encode($condition1),
        ]);

        DB::table('rule_conditions')
            ->where('id', 38)
            ->update([
                'condition' => json_encode($condition2),
        ]);

        DB::table('rule_conditions')
            ->where('id', 39)
            ->update([
                'condition' => json_encode($condition3),
        ]);

        DB::table('rule_conditions')
            ->where('id', 40)
            ->update([
                'condition' => json_encode($condition4),
        ]);
    }

};