<?php

use Illuminate\Support\Facades\DB;
use App\Models\EmloResponse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Rule;

return new class extends Migration
{

    private $seedData =[
                [
                    'name' => 'EDPAnticipation',
                    'param_spec_id' => 1,
                    "active" => 1
                ],

                [
                    'name' => 'EDPConcentrated',
                    'param_spec_id' => 2,
                    "active" => 1
                ],

                [
                    'name' => 'EDPConfident',
                    'param_spec_id' => 3,
                    "active" => 1
                ],

                [
                    'name' => 'EDPEmotional',
                    'param_spec_id' => 4,
                    "active" => 1
                ],

                [
                    'name' => 'EDPEnergetic',
                    'param_spec_id' => 5,
                    "active" => 1
                ],

                [
                    'name' => 'EDPHesitation',
                    'param_spec_id' => 6,
                    "active" => 1
                ],

                [
                    'name' => 'EDPPassionate',
                    'param_spec_id' => 7,
                    "active" => 1
                ],

                [
                    'name' => 'EDPStressful',
                    'param_spec_id' => 8,
                    "active" => 1
                ],

                [
                    'name' => 'EDPThoughtful',
                    'param_spec_id' => 9,
                    "active" => 1
                ],

                [
                    'name' => 'EDPUneasy',
                    'param_spec_id' => 10,
                    "active" => 1
                ],

                [
                    'name' => 'FinalRiskLevel',
                    'param_spec_id' => 11,
                    "active" => 1
                ],

                [
                    'name' => 'SOS',
                    'param_spec_id' => 12,
                    "active" => 1
                ],

                [
                    'name' => 'overallCognitiveActivity',
                    'param_spec_id' => 13,
                    "active" => 1
                ],
            ];


    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Rule::truncate();
        Rule::insert($this->seedData);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Rule::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
