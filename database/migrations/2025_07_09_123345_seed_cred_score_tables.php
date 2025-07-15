<?php

use App\Models\CredScore;
use App\Models\CredScoreKpi;
use App\Models\KpiMetricSpecification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cria o cred_score e captura o ID
        $credScore = CredScore::create([
            'name' => 'test cred score',
            'catalog_id' => 1
        ]);

        // Usa o ID criado para os KPIs
        $credScoreKpis = [
            ['cred_score_id' => $credScore->id],
            ['cred_score_id' => $credScore->id],
        ];
        CredScoreKpi::insert($credScoreKpis);

        // Cria os KPIs e captura os IDs
        $kpiMetricSpecs = [
            [
                'kpi_id' => 1,
                'name' => 'todays mood',
                'question' => 'what is your mood like today',
                'range' => 10,
                'significance' => 2,
            ],
            [
                'kpi_id' => 2,
                'name' => 'todays worries',
                'question' => 'how worried about life are you today',
                'range' => 10,
                'significance' => 1,
            ],
        ];
        KpiMetricSpecification::insert($kpiMetricSpecs);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        KpiMetricSpecification::truncate();
        CredScoreKpi::truncate();
        CredScore::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
