<?php

use App\Models\EmloResponse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Models\EmloResponseParamSpecs;

return new class extends Migration
{

    private $seedData =[
                [
                    'param_name' => 'EDP-Anticipation',
                    'simplified_param_name' => 'Anticipation',
                    'description' => 'Anticipation is the emotional energy your voice reveals when you’re expecting something—whether it’s excitement, curiosity, or a bit of nervousness. It shows how tuned-in and emotionally invested you are in what’s about to happen.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Concentrated',
                    'simplified_param_name' => 'Concentration',
                    'description' => 'Concentration is the focus your voice reveals when your mind is locked in. It reflects how mentally engaged you are—whether you’re deep in thought, absorbing information, or zoning in on something important.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Confident',
                    'simplified_param_name' => 'Confidence',
                    'description' => 'Confidence is the steadiness in your voice that shows how sure you feel. It reflects your sense of certainty, trust in your words, and belief in what you’re saying—whether you’re speaking boldly or with quiet assurance.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Emotional',
                    'simplified_param_name' => 'Emotional',
                    'description' => 'Emotional reflects how much emotional energy and excitement you convey. It captures your responsiveness to what you’re saying, indicating how positively or negatively charged your emotions are. This signal reveals your level of enthusiasm, intensity, and emotional engagement and whether you’re sharing something joyful, meaningful, or deeply important.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Energetic',
                    'simplified_param_name' => 'Energy',
                    'description' => 'Energy is the drive in your voice that shows how alert, lively, or drained you feel. It reflects your overall vibe—whether you’re full of momentum, calmly steady, or running low and needing a recharge.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Hesitation',
                    'simplified_param_name' => 'Hesitation',
                    'description' => 'Hesitation is the pause in your voice that shows when you’re unsure or holding back. It reflects moments of doubt, caution, or the need to think twice before speaking—offering insight into your comfort and confidence.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Passionate',
                    'simplified_param_name' => 'Passion',
                    'description' => 'Passion is the fire in your voice when you truly care about what you’re saying. It reflects strong emotion, deep interest, and personal connection—whether you’re speaking with excitement, urgency, or heartfelt conviction.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Stressful',
                    'simplified_param_name' => 'Stress',
                    'description' => 'Stress is the tension in your voice that shows when you’re feeling pressure, overwhelmed, or stretched thin. It reflects how your body and mind are reacting to challenges—even if you’re not saying it out loud.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Thoughtful',
                    'simplified_param_name' => 'Thoughtfulness',
                    'description' => 'Thoughtfulness is the calm focus in your voice when you’re reflecting, processing, or choosing your words with care. It shows how present and considerate you are—revealing a mind that’s engaged and intentional.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'EDP-Uneasy',
                    'simplified_param_name' => 'Uneasiness',
                    'description' => 'Uneasiness is the tension in your voice that hints at discomfort or embarrassment. It reflects how at ease—or not—you feel with what you’re saying, revealing moments where something doesn’t quite sit right.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'regular',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'gaussian',
                ],

                [
                    'param_name' => 'finalRiskLevel',
                    'simplified_param_name' => 'Risk',
                    'description' => 'Risk is the signal in your voice that suggests you might be holding something back or feeling unsure. It reflects moments where trust, honesty, or emotional safety could be in question—helping highlight when something deeper may be going on.',
                    "min" => 1,
                    "max" => 100,
                    "type" => 'segment',
                    "path_key" => null,
                    "needs_normalization" => false,
                    "distribution" => 'definitive_state',
                ],

                [
                    'param_name' => 'overallCognitiveActivity',
                    'simplified_param_name' => 'Cognitive Balance',
                    'description' => 'Reflects how well your thoughts and emotions are working together. Low balance may signal emotional withdrawal, while high levels may indicate stress or overwhelm. Healthy balance means you’re mentally and emotionally in sync.',
                    "min" => 1,
                    "max" => 2000,
                    "type" => 'regular',
                    "needs_normalization" => true,
                    "path_key" => 'overallCognitiveActivity.averageLevel',
                    "distribution" => 'definitive_state',
                ],

                [
                    'param_name' => 'Aggression',
                    'simplified_param_name' => 'Aggression',
                    'description' => 'Aggression reflects how forceful or intense your communication feels. It ranges from calm and respectful, to assertive and confident, to elevated levels that may suggest frustration or stress. While some firmness can be healthy, higher levels of aggression may be a sign to pause and reflect.',
                    'min' => 0,
                    'max' => 100,
                    'type' => 'regular',
                    'path_key' => 'aggression.averageLevel',
                    "needs_normalization" => false,
                    "distribution" => 'definitive_state',
                ],

                [
                    'param_name' => 'clStress',
                    'simplified_param_name' => 'Stress Recovery',
                    'description' => 'Stress Recovery is the ability to return to a calm, balanced state after experiencing stress—reflected in how your voice reveals the body and mind’s ability to rebound from pressure or tension.',
                    'min' => 0,
                    'max' => 6,
                    'type' => 'regular',
                    'path_key' => 'clStress.clStress',
                    "needs_normalization" => false,
                    "distribution" => 'definitive_state',
                ]
            ];


     private function safeDropForeignKey($table, $column)
    {
        // Check if foreign key exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ?
            AND CONSTRAINT_NAME != 'PRIMARY'
        ", [$table, $column]);

        if (!empty($foreignKeys)) {
            foreach ($foreignKeys as $fk) {
                DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$fk->CONSTRAINT_NAME}");
            }
        }
    }

    public function up(): void
    {
        // Safely drop foreign key
        $this->safeDropForeignKey('rules', 'param_spec_id');

        // Add the type column
        Schema::table('emlo_response_param_specs', function (Blueprint $table) {
            $table->text('type');
            $table->boolean('needs_normalization')->default(false);
            $table->text('path_key')->nullable();
            $table->text('distribution')->nullable();
        });

        // Clear and repopulate data
        DB::table('emlo_response_param_specs')->truncate();
        DB::table('emlo_response_param_specs')->insert($this->seedData);

        // Re-add foreign key constraint
        Schema::table('rules', function (Blueprint $table) {
            $table->foreign('param_spec_id')->references('id')->on('emlo_response_param_specs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safely drop foreign key
        $this->safeDropForeignKey('rules', 'param_spec_id');

        // Remove the type column
        Schema::table('emlo_response_param_specs', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('needs_normalization');
            $table->dropColumn('path_key');
            $table->dropColumn(columns: 'distribution');
        });

        // Clear data
        EmloResponseParamSpecs::truncate();

        // Re-add foreign key constraint
        Schema::table('rules', function (Blueprint $table) {
            $table->foreign('param_spec_id')->references('id')->on('emlo_response_param_specs');
        });
    }
};

