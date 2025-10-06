<?php

use App\Models\EmloResponseParamSpecs;
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
        Schema::table('emlo_param_specs', function (Blueprint $table) {
            EmloResponseParamSpecs::insert(
                [
                    'param_name' => 'risk1',
                    'simplified_param_name' => 'risk1',
                    'description' => 'Risk is the signal in your voice that suggests hesitation, uncertainty, or holding back from full self-honesty.',
                    'emoji' => 'U+1F3AD',
                    'min' => 1,
                    'max' => 100,
                    'type' => 'segment',
                    'needs_normalization' => 1,
                    'distribution' => 'definitive_state'
                ]
            );
        });
    }
};
