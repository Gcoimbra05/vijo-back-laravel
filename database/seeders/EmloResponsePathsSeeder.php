<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EmloResponsePathsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allPaths = [
            ['path_key' => 'reports', 'json_path' => '0.data.reports', 'data_type' => 'arr'],
            ['path_key' => 'channel-0', 'json_path' => '0.data.reports.channel-0', 'data_type' => 'arr'],
            ['path_key' => 'callPriority', 'json_path' => '0.data.reports.channel-0.callPriority', 'data_type' => 'arr'],

            ['path_key' => 'distressPriority', 'json_path' => '0.data.reports.channel-0.callPriority.distressPriority', 'data_type' => 'int'],
            ['path_key' => 'finalCallPriority', 'json_path' => '0.data.reports.channel-0.callPriority.finalCallPriority', 'data_type' => 'int'],
            ['path_key' => 'maxCallPriority', 'json_path' => '0.data.reports.channel-0.callPriority.maxCallPriority', 'data_type' => 'int'],
            ['path_key' => 'tonePriority', 'json_path' => '0.data.reports.channel-0.callPriority.tonePriority', 'data_type' => 'int'],

            ['path_key' => 'edp', 'json_path' => '0.data.reports.channel-0.edp', 'data_type' => 'arr'],

            ['path_key' => 'EDP-Anticipation', 'json_path' => '0.data.reports.channel-0.edp.EDP-Anticipation', 'data_type' => 'int'],
            ['path_key' => 'EDP-Concentrated', 'json_path' => '0.data.reports.channel-0.edp.EDP-Concentrated', 'data_type' => 'int'],
            ['path_key' => 'EDP-Confident', 'json_path' => '0.data.reports.channel-0.edp.EDP-Confident', 'data_type' => 'int'],
            ['path_key' => 'EDP-Emotional', 'json_path' => '0.data.reports.channel-0.edp.EDP-Emotional', 'data_type' => 'int'],
            ['path_key' => 'EDP-Energetic', 'json_path' => '0.data.reports.channel-0.edp.EDP-Energetic', 'data_type' => 'int'],
            ['path_key' => 'EDP-Hesitation', 'json_path' => '0.data.reports.channel-0.edp.EDP-Hesitation', 'data_type' => 'int'],
            ['path_key' => 'EDP-Passionate', 'json_path' => '0.data.reports.channel-0.edp.EDP-Passionate', 'data_type' => 'int'],
            ['path_key' => 'EDP-Stressful', 'json_path' => '0.data.reports.channel-0.edp.EDP-Stressful', 'data_type' => 'int'],
            ['path_key' => 'EDP-Thoughtful', 'json_path' => '0.data.reports.channel-0.edp.EDP-Thoughtful', 'data_type' => 'int'],
            ['path_key' => 'EDP-Uneasy', 'json_path' => '0.data.reports.channel-0.edp.EDP-Uneasy', 'data_type' => 'int'],

            ['path_key' => 'profile', 'json_path' => '0.data.reports.channel-0.profile', 'data_type' => 'arr'],
            ['path_key' => 'aggression', 'json_path' => '0.data.reports.channel-0.profile.aggression', 'data_type' => 'arr'],
            ['path_key' => 'aggression.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.aggression.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'aggression.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.aggression.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'aggression.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.aggression.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'aggression.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.aggression.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'aggression.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.aggression.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'anticipation', 'json_path' => '0.data.reports.channel-0.profile.anticipation', 'data_type' => 'arr'],
            ['path_key' => 'anticipation.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.anticipation.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'anticipation.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.anticipation.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'anticipation.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.anticipation.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'anticipation.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.anticipation.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'anticipation.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.anticipation.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'arousal', 'json_path' => '0.data.reports.channel-0.profile.arousal', 'data_type' => 'arr'],
            ['path_key' => 'arousal.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.arousal.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'arousal.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.arousal.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'arousal.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.arousal.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'arousal.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.arousal.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'arousal.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.arousal.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'atmosphere', 'json_path' => '0.data.reports.channel-0.profile.atmosphere', 'data_type' => 'arr'],
            ['path_key' => 'atmosphere._comments', 'json_path' => '0.data.reports.channel-0.profile.atmosphere._comments', 'data_type' => 'string'],
            ['path_key' => 'atmosphere.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.atmosphere.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'atmosphere.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.atmosphere.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'atmosphere.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.atmosphere.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'atmosphere.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.atmosphere.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'atmosphere.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.atmosphere.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'clStress', 'json_path' => '0.data.reports.channel-0.profile.clStress', 'data_type' => 'arr'],
            ['path_key' => 'clStress.clStress', 'json_path' => '0.data.reports.channel-0.profile.clStress.clStress', 'data_type' => 'int'],
            ['path_key' => 'clStress.high', 'json_path' => '0.data.reports.channel-0.profile.clStress.high', 'data_type' => 'int'],
            ['path_key' => 'clStress.low', 'json_path' => '0.data.reports.channel-0.profile.clStress.low', 'data_type' => 'int'],

            ['path_key' => 'concentration', 'json_path' => '0.data.reports.channel-0.profile.concentration', 'data_type' => 'arr'],
            ['path_key' => 'concentration.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.concentration.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'concentration.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.concentration.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'concentration.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.concentration.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'concentration.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.concentration.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'concentration.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.concentration.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'discomfort', 'json_path' => '0.data.reports.channel-0.profile.discomfort', 'data_type' => 'arr'],
            ['path_key' => 'discomfort.uneasyEnd', 'json_path' => '0.data.reports.channel-0.profile.discomfort.uneasyEnd', 'data_type' => 'int'],
            ['path_key' => 'discomfort.uneasyStart', 'json_path' => '0.data.reports.channel-0.profile.discomfort.uneasyStart', 'data_type' => 'int'],

            ['path_key' => 'energy', 'json_path' => '0.data.reports.channel-0.profile.energy', 'data_type' => 'arr'],
            ['path_key' => 'energy.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.energy.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'energy.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.energy.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'energy.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.energy.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'energy.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.energy.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'energy.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.energy.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'excitement', 'json_path' => '0.data.reports.channel-0.profile.excitement', 'data_type' => 'arr'],
            ['path_key' => 'excitement._comments', 'json_path' => '0.data.reports.channel-0.profile.excitement._comments', 'data_type' => 'string'],
            ['path_key' => 'excitement.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.excitement.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'excitement.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.excitement.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'excitement.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.excitement.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'excitement.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.excitement.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'excitement.normalReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.excitement.normalReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'hesitation', 'json_path' => '0.data.reports.channel-0.profile.hesitation', 'data_type' => 'arr'],
            ['path_key' => 'hesitation._comments', 'json_path' => '0.data.reports.channel-0.profile.hesitation._comments', 'data_type' => 'string'],
            ['path_key' => 'hesitation.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.hesitation.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'hesitation.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.hesitation.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'hesitation.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.hesitation.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'hesitation.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.hesitation.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'hesitation.normalReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.hesitation.normalReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'imagination', 'json_path' => '0.data.reports.channel-0.profile.imagination', 'data_type' => 'arr'],
            ['path_key' => 'imagination.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.imagination.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'imagination.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.imagination.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'imagination.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.imagination.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'imagination.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.imagination.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'imagination.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.imagination.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'joy', 'json_path' => '0.data.reports.channel-0.profile.joy', 'data_type' => 'arr'],
            ['path_key' => 'joy.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.joy.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'joy.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.joy.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'joy.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.joy.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'joy.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.joy.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'joy.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.joy.noReactionPercentage', 'data_type' => 'float'],


            ['path_key' => 'mentalEfficiency', 'json_path' => '0.data.reports.channel-0.profile.mentalEfficiency', 'data_type' => 'arr'],
            ['path_key' => 'mentalEfficiency.bioAverage', 'json_path' => '0.data.reports.channel-0.profile.mentalEfficiency.bioAverage', 'data_type' => 'int'],
            ['path_key' => 'mentalEfficiency.bioHigh', 'json_path' => '0.data.reports.channel-0.profile.mentalEfficiency.bioHigh', 'data_type' => 'int'],
            ['path_key' => 'mentalEfficiency.bioLow', 'json_path' => '0.data.reports.channel-0.profile.mentalEfficiency.bioLow', 'data_type' => 'int'],
            ['path_key' => 'mentalEfficiency.mentalEffortEfficiency', 'json_path' => '0.data.reports.channel-0.profile.mentalEfficiency.mentalEffortEfficiency', 'data_type' => 'int'],

            ['path_key' => 'mentalEffort', 'json_path' => '0.data.reports.channel-0.profile.mentalEffort', 'data_type' => 'arr'],
            ['path_key' => 'mentalEffort.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.mentalEffort.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'mentalEffort.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.mentalEffort.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'mentalEffort.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.mentalEffort.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'mentalEffort.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.mentalEffort.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'mentalEffort.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.mentalEffort.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'overallCognitiveActivity', 'json_path' => '0.data.reports.channel-0.profile.overallCognitiveActivity', 'data_type' => 'arr'],
            ['path_key' => 'overallCognitiveActivity.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.overallCognitiveActivity.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'overallCognitiveActivity.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.overallCognitiveActivity.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'overallCognitiveActivity.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.overallCognitiveActivity.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'overallCognitiveActivity.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.overallCognitiveActivity.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'overallCognitiveActivity.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.overallCognitiveActivity.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'sad', 'json_path' => '0.data.reports.channel-0.profile.sad', 'data_type' => 'arr'],
            ['path_key' => 'sad.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.sad.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'sad.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.sad.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'sad.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.sad.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'sad.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.sad.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'sad.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.sad.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'stress', 'json_path' => '0.data.reports.channel-0.profile.stress', 'data_type' => 'arr'],
            ['path_key' => 'stress.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.stress.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'stress.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.stress.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'stress.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.stress.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'stress.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.stress.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'stress.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.stress.noReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'uncertainty', 'json_path' => '0.data.reports.channel-0.profile.uncertainty', 'data_type' => 'arr'],
            ['path_key' => 'uncertainty._comments', 'json_path' => '0.data.reports.channel-0.profile.uncertainty._comments', 'data_type' => 'string'],
            ['path_key' => 'uncertainty.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.uncertainty.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'uncertainty.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.uncertainty.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'uncertainty.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.uncertainty.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'uncertainty.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.uncertainty.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'uncertainty.normalReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.uncertainty.normalReactionPercentage', 'data_type' => 'float'],

            ['path_key' => 'uneasy', 'json_path' => '0.data.reports.channel-0.profile.uneasy', 'data_type' => 'arr'],
            ['path_key' => 'uneasy.averageLevel', 'json_path' => '0.data.reports.channel-0.profile.uneasy.averageLevel', 'data_type' => 'float'],
            ['path_key' => 'uneasy.highPercentage', 'json_path' => '0.data.reports.channel-0.profile.uneasy.highPercentage', 'data_type' => 'float'],
            ['path_key' => 'uneasy.lowPercentage', 'json_path' => '0.data.reports.channel-0.profile.uneasy.lowPercentage', 'data_type' => 'float'],
            ['path_key' => 'uneasy.midPercentage', 'json_path' => '0.data.reports.channel-0.profile.uneasy.midPercentage', 'data_type' => 'float'],
            ['path_key' => 'uneasy.noReactionPercentage', 'json_path' => '0.data.reports.channel-0.profile.uneasy.noReactionPercentage', 'data_type' => 'float'],

            // riskSummary
            ['path_key' => 'riskSummary', 'json_path' => '0.data.reports.channel-0.riskSummary', 'data_type' => 'arr'],
            ['path_key' => 'averageRiskOZ3', 'json_path' => '0.data.reports.channel-0.riskSummary.averageRiskOZ3', 'data_type' => 'int'],
            ['path_key' => 'riskCounter1', 'json_path' => '0.data.reports.channel-0.riskSummary.riskCounter1', 'data_type' => 'int'],
            ['path_key' => 'riskCounter2', 'json_path' => '0.data.reports.channel-0.riskSummary.riskCounter2', 'data_type' => 'int'],
            ['path_key' => 'riskCounter3', 'json_path' => '0.data.reports.channel-0.riskSummary.riskCounter3', 'data_type' => 'int'],
            ['path_key' => 'riskOZCounter', 'json_path' => '0.data.reports.channel-0.riskSummary.riskOZCounter', 'data_type' => 'int'],

            // tags
            ['path_key' => 'tags', 'json_path' => '0.data.reports.channel-0.tags', 'data_type' => 'list'],
            ['path_key' => 'tags[0]', 'json_path' => '0.data.reports.channel-0.tags[0]', 'data_type' => 'string'],
            ['path_key' => 'tags[1]', 'json_path' => '0.data.reports.channel-0.tags[1]', 'data_type' => 'string'],
            ['path_key' => 'tags[2]', 'json_path' => '0.data.reports.channel-0.tags[2]', 'data_type' => 'string'],
            ['path_key' => 'tags[3]', 'json_path' => '0.data.reports.channel-0.tags[3]', 'data_type' => 'string'],
            ['path_key' => 'tags[4]', 'json_path' => '0.data.reports.channel-0.tags[4]', 'data_type' => 'string'],

            // testReport
            ['path_key' => 'testReport', 'json_path' => '0.data.reports.channel-0.testReport', 'data_type' => 'arr'],
            ['path_key' => 'biomarkers', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers', 'data_type' => 'arr'],
            ['path_key' => 'biomarkers.averageCognitionLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.averageCognitionLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.averageEmotionLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.averageEmotionLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.averageStressLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.averageStressLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.cognitiveChangeLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.cognitiveChangeLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.concetrationChangeLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.concetrationChangeLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.emotionChangeLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.emotionChangeLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.energyChangeLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.energyChangeLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.engagedChangeLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.engagedChangeLevel', 'data_type' => 'int'],
            ['path_key' => 'biomarkers.stressChangeLevel', 'json_path' => '0.data.reports.channel-0.testReport.biomarkers.stressChangeLevel', 'data_type' => 'int'],

            ['path_key' => 'testReport.extremeEmotionSegments', 'json_path' => '0.data.reports.channel-0.testReport.extremeEmotionSegments', 'data_type' => 'int'],
            ['path_key' => 'testReport.extremeStressConversationPortions', 'json_path' => '0.data.reports.channel-0.testReport.extremeStressConversationPortions', 'data_type' => 'int'],
            ['path_key' => 'testReport.extremeStressSegments', 'json_path' => '0.data.reports.channel-0.testReport.extremeStressSegments', 'data_type' => 'int'],

            ['path_key' => 'summary', 'json_path' => '0.data.reports.summary', 'data_type' => 'arr'],
            ['path_key' => 'summary.channel-0', 'json_path' => '0.data.reports.summary.channel-0', 'data_type' => 'arr'],
            ['path_key' => 'summary.channel-0.CSCscore', 'json_path' => '0.data.reports.summary.channel-0.CSCscore', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.angerPercentage', 'json_path' => '0.data.reports.summary.channel-0.angerPercentage', 'data_type' => 'int'],

            ['path_key' => 'summary.channel-0.callLength', 'json_path' => '0.data.reports.summary.channel-0.callLength', 'data_type' => 'arr'],
            ['path_key' => 'summary.channel-0.callLength.minutes', 'json_path' => '0.data.reports.summary.channel-0.callLength.minutes', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.callLength.seconds', 'json_path' => '0.data.reports.summary.channel-0.callLength.seconds', 'data_type' => 'int'],

            ['path_key' => 'summary.channel-0.channelAgentPriority', 'json_path' => '0.data.reports.summary.channel-0.channelAgentPriority', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.channelDistressPriority', 'json_path' => '0.data.reports.summary.channel-0.channelDistressPriority', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.channelFinalPriority', 'json_path' => '0.data.reports.summary.channel-0.channelFinalPriority', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.channelMaxPriority', 'json_path' => '0.data.reports.summary.channel-0.channelMaxPriority', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.code', 'json_path' => '0.data.reports.summary.channel-0.code', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.corroboratedAngerPercentage', 'json_path' => '0.data.reports.summary.channel-0.corroboratedAngerPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.corroboratedStressPercentage', 'json_path' => '0.data.reports.summary.channel-0.corroboratedStressPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.dissatisfaction', 'json_path' => '0.data.reports.summary.channel-0.dissatisfaction', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.energyAverage', 'json_path' => '0.data.reports.summary.channel-0.energyAverage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.final10Score', 'json_path' => '0.data.reports.summary.channel-0.final10Score', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.highEnergyPercentage', 'json_path' => '0.data.reports.summary.channel-0.highEnergyPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.joyPercentage', 'json_path' => '0.data.reports.summary.channel-0.joyPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.lowAggressionPercentage', 'json_path' => '0.data.reports.summary.channel-0.lowAggressionPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.lowEnergyPercentage', 'json_path' => '0.data.reports.summary.channel-0.lowEnergyPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.mediumEnergyPercentage', 'json_path' => '0.data.reports.summary.channel-0.mediumEnergyPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.sadPercentage', 'json_path' => '0.data.reports.summary.channel-0.sadPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.segmentsCount', 'json_path' => '0.data.reports.summary.channel-0.segmentsCount', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.stressPercentage', 'json_path' => '0.data.reports.summary.channel-0.stressPercentage', 'data_type' => 'int'],
            ['path_key' => 'summary.channel-0.volumeAverage', 'json_path' => '0.data.reports.summary.channel-0.volumeAverage', 'data_type' => 'int'],

            // general
            ['path_key' => 'summary.general', 'json_path' => '0.data.reports.summary.general', 'data_type' => 'arr'],
            ['path_key' => 'summary.general.code', 'json_path' => '0.data.reports.summary.general.code', 'data_type' => 'string'],
            ['path_key' => 'summary.general.priority', 'json_path' => '0.data.reports.summary.general.priority', 'data_type' => 'int'],
        ];

        // Chunk insertion for better performance when dealing with large datasets
        $chunks = array_chunk($allPaths, 50);
        
        // Iterate over chunks and insert them
        foreach ($chunks as $chunk) {
            $insertData = [];
            foreach ($chunk as $path) {
                $insertData[] = [
                    'path_key' => $path['path_key'],
                    'json_path' => $path['json_path'],
                    'data_type' => $path['data_type'],
                ];
            }
            
            // Bulk insert the chunk
            DB::table('emlo_response_paths')->insertOrIgnore($insertData);
        }
        
        $this->command->info('Inserted ' . count($allPaths) . ' segments into emlo_response_paths table.');
    }
}
