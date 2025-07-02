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
            EmloResponsePathsSeeder::class,
            EmloResponseSegmentSeeder::class,
            EmloResponseSegmentsSeeder::class,
            # LlmTemplatesSeeder::class,
            VideoRequestsSeeder::class,
        ]);
    }
}
