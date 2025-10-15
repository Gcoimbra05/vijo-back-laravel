<?php

use App\Models\Catalog;
use App\Models\KpiMetricSpecification;
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
        KpiMetricSpecification::insert([

            [

                'kpi_id' => 11,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 12,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 13,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 14,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 15,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 16,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 17,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 18,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 19,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 20,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [

                'kpi_id' => 21,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 22,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 23,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 24,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 25,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 26,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 27,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 28,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],


            [

                'kpi_id' => 29,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ],

            [
                'kpi_id' => 30,
                'emlo_param_spec_id' => 15,
                'range' => 100,
                'significance' => 1
            ]


            
        ]);

            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
