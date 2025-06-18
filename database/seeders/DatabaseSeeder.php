<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AdditionalRulesSeeder::class,
            EmloResponseParamSpecsSeeder::class,
            EmloResponsePathsSeeder::class,
            EmloResponseSegmentSeeder::class,
            EmloResponseSegmentsSeeder::class,
            # LlmTemplatesSeeder::class,
            RuleConditionSeeder::class,
            RuleSeeder::class,
            VideoRequestsSeeder::class,
        ]);
    }
}
