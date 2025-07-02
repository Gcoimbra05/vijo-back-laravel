<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Models\RuleCondition;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

   private function seedData() { 
            
        return [
            [
                'rule_id' => 1,
                'condition' => json_encode([
                    "type" => "simple",
                    "param" => "EDP-Anticipation",
                    "operator" => "<", 
                    "value" => 31
                ]),
                'order_index' => 1,
                'emotion_performance' => 'Normal',
                'message' => 'Calm and balanced. Anticipation reflects an individual’s expectation and mental preparation for future interactions or outcomes. It reveals how connected someone feels to their environment—whether they are attentively engaged, looking forward to what’s next, or feeling apprehensive about upcoming events or reactions from others.',
                'active' => true,
            ],

            [
                'rule_id' => 1,
                'condition' => json_encode([
                    "type" => "compound",
                    "operator" => "AND", 
                    "conditions" => [
                        ["param" => "EDP-Anticipation", "operator" => ">", "value" => 31], 
                        ["param" => "EDP-Anticipation", "operator" => "<", "value" => 51]
                    ]
                ]),
                'order_index' => 2,
                'emotion_performance' => 'Above Normal',
                'message' => 'Heightened alertness. Anticipation reflects an individual’s expectation and readiness for future interactions or outcomes. At this level, it reveals increased emotional and cognitive engagement—where the person is highly attentive, possibly feeling excited, uncertain, or apprehensive about what’s to come.',
                'active' => true,
            ],

            [
                'rule_id' => 1,
                'condition' => json_encode([
                    "type" => "compound",
                    "operator" => "AND", 
                    "conditions" => [
                        ["param" => "EDP-Anticipation", "operator" => ">", "value" => 51], 
                        ["param" => "EDP-Anticipation", "operator" => "<", "value" => 100]
                    ]
                ]),
                'order_index' => 3,
                'emotion_performance' => 'High',
                'message' => 'Over-anticipation and high anxiety. Anticipation reflects an individual’s expectation and preparation for upcoming interactions or outcomes. At this level, it indicates an intense emotional and cognitive response—where the person may feel overwhelmed, overly alert, or fixated on what’s ahead. Higher values suggest heightened tension, fear of outcomes, or emotional overinvestment in future events.',
                'active' => true,
            ]
        ];

   }


    public function up(): void
    {
        Schema::table('rule_conditions', function (Blueprint $table){
            $table->text('emotion_performance')->nullable();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        RuleCondition::truncate();
        RuleCondition::insert($this->seedData());

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('rule_conditions', function (Blueprint $table){
            $table->dropColumn('emotion_performance');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        RuleCondition::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
