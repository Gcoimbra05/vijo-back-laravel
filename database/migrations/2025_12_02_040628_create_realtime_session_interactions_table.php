<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('realtime_session_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realtime_session_id')->constrained()->cascadeOnDelete();

            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);

            $table->text('api_response');

            $table->timestamps();

            $table->index(
                ['realtime_session_id', 'created_at'],
                'rlt_sess_int_created_idx'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('realtime_session_interactions');
    }
};
