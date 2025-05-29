<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EmloResponseSegmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
    {
        $allSegments = [
            // First 127 regular segments
            ['number' => 0, 'name' => 'index'],
            ['number' => 1, 'name' => 'channel'],
            ['number' => 2, 'name' => 'startPosSec'],
            ['number' => 3, 'name' => 'endPosSec'],
            ['number' => 4, 'name' => 'validSegment'],
            ['number' => 5, 'name' => 'topics'],
            ['number' => 6, 'name' => 'question'],
            ['number' => 7, 'name' => 'onlineLVA'],
            ['number' => 8, 'name' => 'risk1'],
            ['number' => 9, 'name' => 'risk2'],
            ['number' => 10, 'name' => 'riskOZ'],
            ['number' => 11, 'name' => 'oz1'],
            ['number' => 12, 'name' => 'oz2'],
            ['number' => 13, 'name' => 'oz3'],
            ['number' => 14, 'name' => 'energy'],
            ['number' => 15, 'name' => 'joy'],
            ['number' => 16, 'name' => 'sad'],
            ['number' => 17, 'name' => 'aggression'],
            ['number' => 18, 'name' => 'stress'],
            ['number' => 19, 'name' => 'uncertainty'],
            ['number' => 20, 'name' => 'excitement'],
            ['number' => 21, 'name' => 'uneasy'],
            ['number' => 22, 'name' => 'concentration'],
            ['number' => 23, 'name' => 'anticipation'],
            ['number' => 24, 'name' => 'hesitation'],
            ['number' => 25, 'name' => 'emotionBalance'],
            ['number' => 26, 'name' => 'emotionEnergyBalance'],
            ['number' => 27, 'name' => 'mentalEffort'],
            ['number' => 28, 'name' => 'imagination'],
            ['number' => 29, 'name' => 'arousal'],
            ['number' => 30, 'name' => 'overallCognitiveActivity'],
            ['number' => 31, 'name' => 'emotionCognitiveRatio'],
            ['number' => 32, 'name' => 'extremeEmotion'],
            ['number' => 33, 'name' => 'atmosphere'],
            ['number' => 34, 'name' => 'cognitiveHighLowBalance'],
            ['number' => 35, 'name' => 'voiceEnergy'],
            ['number' => 36, 'name' => 'dissatisfied'],
            ['number' => 37, 'name' => 'LVA-GlobalStress'],
            ['number' => 38, 'name' => 'LVA-EmotionStress'],
            ['number' => 39, 'name' => 'LVA-CognitiveStress'],
            ['number' => 40, 'name' => 'LVA-EnergyStress '],
            ['number' => 41, 'name' => 'LVA-MentalEffort'],
            ['number' => 42, 'name' => 'LVA-SOSStress'],
            ['number' => 43, 'name' => 'EmotionPlayer-Energy'],

            ['number' => 44, 'name' => 'EmotionPlayer-Joy'],
            ['number' => 45, 'name' => 'EmotionPlayer-Sad'],
            ['number' => 46, 'name' => 'EmotionPlayer-Aggression'],

            ['number' => 47, 'name' => 'EmotionPlayer-Stress'],
            ['number' => 48, 'name' => 'EmotionPlayer-Risk'],
            ['number' => 49, 'name' => 'finalRiskLevel'],
            ['number' => 50, 'name' => 'EDP-Energetic'],
            ['number' => 51, 'name' => 'EDP-Passionate'],
            ['number' => 52, 'name' => 'EDP-Emotional'],
            ['number' => 53, 'name' => 'EDP-Uneasy'],
            ['number' => 54, 'name' => 'EDP-Stressful'],
            ['number' => 55, 'name' => 'EDP-Thoughtful'],
            ['number' => 56, 'name' => 'EDP-Confident'],
            ['number' => 57, 'name' => 'EDP-Concentrated'],
            ['number' => 58, 'name' => 'EDP-Anticipation'],
            ['number' => 59, 'name' => 'EDP-Hesitation'],
            ['number' => 60, 'name' => 'callPriority'],
            ['number' => 61, 'name' => 'callPriorityAgent'],
            ['number' => 62, 'name' => 'callDistressPriority'],
            ['number' => 63, 'name' => 'VOL1'],
            ['number' => 64, 'name' => 'VOL2'],
            ['number' => 65, 'name' => 'JQcl'],
            ['number' => 66, 'name' => 'SOS'],
            ['number' => 67, 'name' => 'AVJ'],
            ['number' => 68, 'name' => 'CHL'],
            ['number' => 69, 'name' => 'Fant'],
            ['number' => 70, 'name' => 'Fcen'],
            ['number' => 71, 'name' => 'Fflic'],
            ['number' => 72, 'name' => 'Fmain'],
            ['number' => 73, 'name' => 'FmainPos'],
            ['number' => 74, 'name' => 'Fq'],
            ['number' => 75, 'name' => 'FsubCog'],
            ['number' => 76, 'name' => 'FsubEmo'],
            ['number' => 77, 'name' => 'Fx'],
            ['number' => 78, 'name' => 'JQ'],
            ['number' => 79, 'name' => 'LJ'],
            ['number' => 80, 'name' => 'MaxVolAmp'],
            ['number' => 81, 'name' => 'sampleSize'],
            ['number' => 82, 'name' => 'P1'],
            ['number' => 83, 'name' => 'P2'],
            ['number' => 84, 'name' => 'P3'],
            ['number' => 85, 'name' => 'SPJ'],
            ['number' => 86, 'name' => 'SPJhl'],
            ['number' => 87, 'name' => 'SPJll'],
            ['number' => 88, 'name' => 'SPJsh'],
            ['number' => 89, 'name' => 'SPJsl'],
            ['number' => 90, 'name' => 'SPT'],
            ['number' => 91, 'name' => 'SPST'],
            ['number' => 92, 'name' => 'SPBT'],


            ['number' => 93, 'name' => 'SPBth'],
            ['number' => 94, 'name' => 'SPBtl'],
            ['number' => 95, 'name' => 'SPSth'],
            ['number' => 96, 'name' => 'SPStl'],
            ['number' => 97, 'name' => 'SPBtl_DIF'],
            ['number' => 98, 'name' => 'SPBth_DIF'],
            ['number' => 99, 'name' => 'lJQ'],
            ['number' => 100, 'name' => 'mJQ'],
            ['number' => 101, 'name' => 'hJQ'],
            ['number' => 102, 'name' => 'SPJsav'],
            ['number' => 103, 'name' => 'SPJlav'],
            ['number' => 104, 'name' => 'intCHL'],
            ['number' => 105, 'name' => 'SPTJtot'],
            ['number' => 106, 'name' => 'SPJdist'],
            ['number' => 107, 'name' => 'SPJcomp'],
            ['number' => 108, 'name' => 'JHLratio'],
            ['number' => 109, 'name' => 'nCHL'],
            ['number' => 110, 'name' => 'CHLdif'],
            ['number' => 111, 'name' => 'CCCHL'],
            ['number' => 112, 'name' => 'sptBdiff'],
            ['number' => 113, 'name' => 'HASv'],
            ['number' => 114, 'name' => 'AVJcl'],
            ['number' => 115, 'name' => 'cPor'],
            ['number' => 116, 'name' => 'feelGPT'],
            ['number' => 117, 'name' => 'GPTCommand'],
            ['number' => 118, 'name' => 'offlineLVA-value'],
            ['number' => 119, 'name' => 'offlineLVA-riskStress'],
            ['number' => 120, 'name' => 'offlineLVA-riskProbability'],
            ['number' => 121, 'name' => 'offlineLVA-emotionStress'],
            ['number' => 122, 'name' => 'offlineLVA-cognitiveStress'],
            ['number' => 123, 'name' => 'offlineLVA-globalStress'],
            ['number' => 124, 'name' => 'offlineLVA-frgStress'],
            ['number' => 125, 'name' => 'offlineLVA-subjectiveEffortLevel'],
            ['number' => 126, 'name' => 'offlineLVA-deceptionPatterns'],

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
