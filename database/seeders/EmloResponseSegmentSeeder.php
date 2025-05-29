<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EmloResponseSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $allSegments = [
            // First 127 regular segments
            ['number' => 0, 'name' => 'segment_no'],
            ['number' => 1, 'name' => 'channel'],
            ['number' => 2, 'name' => 'start_pos_sec'],
            ['number' => 3, 'name' => 'end_pos_sec'],
            ['number' => 4, 'name' => 'validSegment'],
            ['number' => 5, 'name' => 'topic'],
            ['number' => 6, 'name' => 'question'],
            ['number' => 7, 'name' => 'online_lva'],
            ['number' => 8, 'name' => 'risk1'],
            ['number' => 9, 'name' => 'risk2'],
            ['number' => 10, 'name' => 'riskOZ'],

            ['number' => 14, 'name' => 'energy'],
            ['number' => 15, 'name' => 'content'],
            ['number' => 16, 'name' => 'upset'],
            ['number' => 17, 'name' => 'angry'],
            ['number' => 18, 'name' => 'stress'],
            ['number' => 19, 'name' => 'uncertainty'],
            ['number' => 20, 'name' => 'uneasy'],
            ['number' => 21, 'name' => 'emotional'],
            ['number' => 22, 'name' => 'concentration'],
            ['number' => 23, 'name' => 'anticipation'],
            ['number' => 24, 'name' => 'hesitation'],
            ['number' => 25, 'name' => 'emoBalance'],
            ['number' => 26, 'name' => 'emoEnergyBalance'],
            ['number' => 27, 'name' => 'mentalEffort'],
            ['number' => 28, 'name' => 'imagin'],
            ['number' => 29, 'name' => 'sAF'],
            ['number' => 30, 'name' => 'oCA'],
            ['number' => 31, 'name' => 'emoCogRatio'],
            ['number' => 32, 'name' => 'extremeEmotion'],
            ['number' => 33, 'name' => 'atmosphere'],
            ['number' => 34, 'name' => 'cogHighLowBalance'],
            ['number' => 35, 'name' => 'voice_energy'],
            ['number' => 36, 'name' => 'dissat'],
            ['number' => 37, 'name' => 'lVAGLBStress'],
            ['number' => 38, 'name' => 'lVAEmoStress'],
            ['number' => 39, 'name' => 'lVACOGStress'],
            ['number' => 40, 'name' => 'lVAENRStress'],
            ['number' => 41, 'name' => 'lVAMentalEffort'],
            ['number' => 42, 'name' => 'lVASOSSTRESS'],
            ['number' => 43, 'name' => 'emoPlayerEnergy'],

            ['number' => 44, 'name' => 'emoPlayerJoy'],
            ['number' => 45, 'name' => 'emoPlayerSad'],
            ['number' => 46, 'name' => 'emoPlayerAggression'],

            ['number' => 47, 'name' => 'emoPlayerStress'],
            ['number' => 48, 'name' => 'emoPlayerRisk'],
            ['number' => 49, 'name' => 'finalRiskLevel'],
            ['number' => 50, 'name' => 'EDPEnergetic'],
            ['number' => 51, 'name' => 'EDPPassionate'],
            ['number' => 52, 'name' => 'EDPEmotional'],
            ['number' => 53, 'name' => 'EDPUneasy'],
            ['number' => 54, 'name' => 'EDPStressful'],
            ['number' => 55, 'name' => 'EDPThoughtful'],
            ['number' => 56, 'name' => 'EDPConfident'],
            ['number' => 57, 'name' => 'EDPConcentrated'],
            ['number' => 58, 'name' => 'EDPAnticipation'],
            ['number' => 59, 'name' => 'EDPHesitation'],
            ['number' => 60, 'name' => 'callPriority'],
            ['number' => 61, 'name' => 'callPriorityAgent'],
            ['number' => 62, 'name' => 'callDistressPriority'],
            ['number' => 63, 'name' => 'vOL1'],
            ['number' => 64, 'name' => 'vOL2'],
            ['number' => 65, 'name' => 'jQcl'],
            ['number' => 66, 'name' => 'sOS'],
            ['number' => 67, 'name' => 'aVJ'],
            ['number' => 68, 'name' => 'cHL'],
            ['number' => 69, 'name' => 'fant'],
            ['number' => 70, 'name' => 'fcen'],
            ['number' => 71, 'name' => 'fflic'],
            ['number' => 72, 'name' => 'fmain'],
            ['number' => 73, 'name' => 'fmainPos'],
            ['number' => 74, 'name' => 'fq'],
            ['number' => 75, 'name' => 'fsubCog'],
            ['number' => 76, 'name' => 'fsubEmo'],
            ['number' => 77, 'name' => 'fx'],
            ['number' => 78, 'name' => 'jQ'],
            ['number' => 79, 'name' => 'lJ'],
            ['number' => 80, 'name' => 'maxVolAmp'],
            ['number' => 81, 'name' => 'sampleSize'],
            ['number' => 82, 'name' => 'p1'],
            ['number' => 83, 'name' => 'p2'],
            ['number' => 84, 'name' => 'p3'],
            ['number' => 85, 'name' => 'sPJ'],
            ['number' => 86, 'name' => 'sPJhl'],
            ['number' => 87, 'name' => 'sPJll'],
            ['number' => 88, 'name' => 'sPJsh'],
            ['number' => 89, 'name' => 'sPJsl'],
            ['number' => 90, 'name' => 'sPT'],
            ['number' => 91, 'name' => 'sPST'],
            ['number' => 92, 'name' => 'sPBT'],


            ['number' => 93, 'name' => 'sPBth'],
            ['number' => 94, 'name' => 'sPBtl'],
            ['number' => 95, 'name' => 'sPSth'],
            ['number' => 96, 'name' => 'sPStl'],
            ['number' => 97, 'name' => 'sPBtl_DIF'],
            ['number' => 98, 'name' => 'sPBth_DIF'],
            ['number' => 99, 'name' => 'lJQ'],
            ['number' => 100, 'name' => 'mJQ'],
            ['number' => 101, 'name' => 'hJQ'],
            ['number' => 102, 'name' => 'sPJsav'],
            ['number' => 103, 'name' => 'sPJlav'],
            ['number' => 104, 'name' => 'intCHL'],
            ['number' => 105, 'name' => 'sPTJtot'],
            ['number' => 106, 'name' => 'sPJdist'],
            ['number' => 107, 'name' => 'sPJcomp'],
            ['number' => 108, 'name' => 'jHLratio'],
            ['number' => 109, 'name' => 'nCHL'],
            ['number' => 110, 'name' => 'cHLdif'],
            ['number' => 111, 'name' => 'cCCHL'],
            ['number' => 112, 'name' => 'sptBdiff'],
            ['number' => 113, 'name' => 'hASv'],
            ['number' => 114, 'name' => 'aVJcl'],
            ['number' => 115, 'name' => 'cPor'],
            ['number' => 116, 'name' => 'feelGPT'],
            ['number' => 117, 'name' => 'GPTCommand'],
            ['number' => 118, 'name' => 'offlineLVAValue'],
            ['number' => 119, 'name' => 'offlineLVARiskStress'],
            ['number' => 120, 'name' => 'offlineLVARiskProbability'],
            ['number' => 121, 'name' => 'offlineLVARiskEmotionStress'],
            ['number' => 122, 'name' => 'offlineLVARiskCognitiveStress'],
            ['number' => 123, 'name' => 'offlineLVARiskGlobalStress'],
            ['number' => 124, 'name' => 'sPBofflineLVARiskFrgStressT'],
            ['number' => 125, 'name' => 'offlineLVARiskSubjectiveEffortLevel'],
            ['number' => 126, 'name' => 'offlineLVARiskDeceptionPatterns'],

            // zero value fields
            ['number' => 127, 'name' => 'lVARiskStress'],
            ['number' => 128, 'name' => 'offline_lva'],
            ['number' => 129, 'name' => 'iThink'],
            ['number' => 130, 'name' => 'aF1'],
            ['number' => 131, 'name' => 'aF2'],
            ['number' => 132, 'name' => 'aF3'],
            ['number' => 133, 'name' => 'aF4'],
            ['number' => 134, 'name' => 'aF5'],
            ['number' => 135, 'name' => 'aF6'],
            ['number' => 136, 'name' => 'aF7'],
            ['number' => 137, 'name' => 'aF8'],
            ['number' => 138, 'name' => 'aF9'],
            ['number' => 139, 'name' => 'aF10'],
        ];

        // Chunk insertion for better performance when dealing with large datasets
        $chunks = array_chunk($allSegments, 50);
        
        // Iterate over chunks and insert them
        foreach ($chunks as $chunk) {
            $insertData = [];
            foreach ($chunk as $segment) {
                $insertData[] = [
                    'name' => $segment['name'],
                    'number' => $segment['number'],
                ];
            }
            
            // Bulk insert the chunk
            DB::table('emlo_response_segments')->insertOrIgnore($insertData);
        }
        
        $this->command->info('Inserted ' . count($allSegments) . ' segments into emlo_response_segments table.');
    }
}
