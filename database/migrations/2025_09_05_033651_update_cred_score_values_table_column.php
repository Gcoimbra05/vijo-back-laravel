<?php

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
        // Verifica se a coluna 'percieved_score' existe antes de renomear
        if (Schema::hasColumn('cred_score_values', 'percieved_score')) {
            Schema::table('cred_score_values', function (Blueprint $table) {
                $table->renameColumn('percieved_score', 'perceived_score');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verifica se a coluna 'perceived_score' existe antes de renomear de volta
        if (Schema::hasColumn('cred_score_values', 'perceived_score')) {
            Schema::table('cred_score_values', function (Blueprint $table) {
                $table->renameColumn('perceived_score', 'percieved_score');
            });
        }
    }
};
