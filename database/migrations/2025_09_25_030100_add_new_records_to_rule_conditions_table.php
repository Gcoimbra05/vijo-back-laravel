<?php

use App\Models\RuleCondition;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rule_conditions', function (Blueprint $table) {
            RuleCondition::insert([
                // Aggression
                [
                    'rule_id' => 14,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "Aggression", "operator" => ">=", "value" => 0], 
                            ["param" => "Aggression", "operator" => "<=", "value" => 10]
                        ]
                    ]),
                    'order_index' => 1,
                    'message' => 'No Aggression Detected: Your voice sounds calm and composed with no signs of anger or frustration',
                    'active' => true,
                ],

                [
                    'rule_id' => 14,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "Aggression", "operator" => ">", "value" => 10], 
                            ["param" => "Aggression", "operator" => "<=", "value" => 28]
                        ]
                    ]),
                    'order_index' => 2,
                    'message' => 'Mild Aggression Detected: Your voice shows some tension and mild frustration coming through in your tone',
                    'active' => true,
                ],

                [
                    'rule_id' => 14,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "Aggression", "operator" => ">", "value" => 28], 
                            ["param" => "Aggression", "operator" => "<=", "value" => 100]
                        ]
                    ]),
                    'order_index' => 3,
                    'message' => 'High Aggression Detected: Your voice indicates significant anger and tension that\'s strongly affecting your communication.',
                    'active' => true,
                ],

                // clStress
                [
                    'rule_id' => 15,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "clStress", "operator" => ">=", "value" => 0], 
                            ["param" => "clStress", "operator" => "<=", "value" => 10]
                        ]
                    ]),
                    'order_index' => 1,
                    'message' => 'Critical Stress Level: Extreme stress detected — professional support is strongly recommended.',
                    'active' => true,
                ],

                [
                    'rule_id' => 15,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "clStress", "operator" => ">", "value" => 10], 
                            ["param" => "clStress", "operator" => "<=", "value" => 25]
                        ]
                    ]),
                    'order_index' => 2,
                    'message' => 'Overwhelmed: Stress remains very high; you may need immediate lifestyle changes or support.',
                    'active' => true,
                ],

                [
                    'rule_id' => 15,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "clStress", "operator" => ">", "value" => 25], 
                            ["param" => "clStress", "operator" => "<=", "value" => 40]
                        ]
                    ]),
                    'order_index' => 3,
                    'message' => 'Struggling to Recover: Stress is lingering; you may need rest, support, or stress reduction strategies.',
                    'active' => true,
                ],

                [
                    'rule_id' => 15,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "clStress", "operator" => ">", "value" => 40], 
                            ["param" => "clStress", "operator" => "<=", "value" => 55]
                        ]
                    ]),
                    'order_index' => 4,
                    'message' => 'Resilient Under Pressure: You manage high stress but should monitor for fatigue and burnout.',
                    'active' => true,
                ],

                [
                    'rule_id' => 15,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "clStress", "operator" => ">", "value" => 55], 
                            ["param" => "clStress", "operator" => "<=", "value" => 70]
                        ]
                    ]),
                    'order_index' => 5,
                    'message' => 'Good Resilience: You recover well from moderate stress with healthy patterns.',
                    'active' => true,
                ],

                [
                    'rule_id' => 15,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "clStress", "operator" => ">", "value" => 70], 
                            ["param" => "clStress", "operator" => "<=", "value" => 85]
                        ]
                    ]),
                    'order_index' => 6,
                    'message' => 'Excellent Resilience: You’re managing stress with strong coping strategies and balance.',
                    'active' => true,
                ],

                [
                    'rule_id' => 15,
                    'condition' => json_encode([
                        "type" => "compound",
                        "operator" => "AND", 
                        "conditions" => [
                            ["param" => "clStress", "operator" => ">", "value" => 85], 
                            ["param" => "clStress", "operator" => "<=", "value" => 100]
                        ]
                    ]),
                    'order_index' => 7,
                    'message' => 'Optimal Recovery:  Your stress recovery looks excellent, but paired with low engagement, this may indicate emotional detachment.',
                    'active' => true,
                ]
            ]);
        });
    }
};
