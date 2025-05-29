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
        Schema::table('emlo_response_values', function (Blueprint $table) {
            $table->dropColumn('value');
            
            // Add the three new columns
            $table->text('string_value')->nullable();
            $table->integer('numeric_value')->nullable(); // Fixed 'int' to 'integer'
            $table->boolean('boolean_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emlo_response_values', function (Blueprint $table) {
            // Drop the new columns
            $table->dropColumn(['string_value', 'numeric_value', 'boolean_value']);
            
            // Re-add the original 'value' column
            $table->text('value')->nullable(); // Assuming the original 'value' column was a text field and nullable
        });
    }
};
