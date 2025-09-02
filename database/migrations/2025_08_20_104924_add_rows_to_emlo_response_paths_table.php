<?php

use App\Models\EmloResponsePath;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $inputData = [
            // edps
            [
                "id" => 9,
                "specId" => 1
            ],
            [
                "id" => 10,
                "specId" => 2
            ],
            [
                "id" => 11,
                "specId" => 3
            ],
            [
                "id" => 12,
                "specId" => 4
            ],
            [
                "id" => 13,
                "specId" => 5
            ],
            [
                "id" => 14,
                "specId" => 6
            ],
            [
                "id" => 15,
                "specId" => 7
            ],
            [
                "id" => 16,
                "specId" => 8
            ],
            [
                "id" => 17,
                "specId" => 9
            ],
            [
                "id" => 18,
                "specId" => 10
            ],



            // def state emotions
            [
                "id" => 102,
                "specId" => 12
            ],
            [
                "id" => 21,
                "specId" => 13
            ],
            [
                "id" => 46,
                "specId" => 14
            ],

            // self honesty commented out bcs it wont work with current setup
            /*
            [
                "id" => 18,
                "specId" => 15
            ],
            */


        ];

        foreach ($inputData as $inputArray) {
            $id = $inputArray['id'];
            $specId = $inputArray['specId'];

            // Option 1: Using Eloquent Model
            EmloResponsePath::where('id', $id)
                ->update(['emlo_param_spec_id' => $specId]);

            // Option 2: Using Query Builder (alternative)
            // DB::table('emlo_response_paths')
            //     ->where('id', $id)
            //     ->update(['emlo_param_spec_id' => $specId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $inputData = [
            // edps
            [
                "id" => 9,
                "specId" => 1
            ],
            [
                "id" => 10,
                "specId" => 2
            ],
            [
                "id" => 11,
                "specId" => 3
            ],
            [
                "id" => 12,
                "specId" => 4
            ],
            [
                "id" => 13,
                "specId" => 5
            ],
            [
                "id" => 14,
                "specId" => 6
            ],
            [
                "id" => 15,
                "specId" => 7
            ],
            [
                "id" => 16,
                "specId" => 8
            ],
            [
                "id" => 17,
                "specId" => 9
            ],
            [
                "id" => 18,
                "specId" => 10
            ],



            // def state emotions
            [
                "id" => 102,
                "specId" => 12
            ],
            [
                "id" => 21,
                "specId" => 13
            ],
            [
                "id" => 46,
                "specId" => 14
            ],

            // self honesty commented out bcs it wont work with current setup
            /*
            [
                "id" => 18,
                "specId" => 15
            ],
            */


        ];

        foreach ($inputData as $inputArray) {
            $id = $inputArray['id'];

            // Reset the emlo_param_spec_id to null or previous value
            EmloResponsePath::where('id', $id)
                ->update(['emlo_param_spec_id' => null]);
        }
    }
};