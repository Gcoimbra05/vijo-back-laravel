<?php

use App\Models\RuleCondition;
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
        Schema::table('rule_conditions', function (Blueprint $table) {
            RuleCondition::where('rule_id', 14)->delete();
            RuleCondition::where('rule_id', 15)->delete();
        });
    }
};
