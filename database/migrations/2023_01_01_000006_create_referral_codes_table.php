<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralCodesTable extends Migration
{
    public function up()
    {
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->unsignedInteger('affiliate_id');
            $table->string('code', 100)->unique()->nullable(false);
            $table->decimal('commission', 10, 2)->nullable();
            $table->integer('number_uses')->default(0);
            $table->integer('max_number_uses')->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();

            // Define foreign key
            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('referral_codes');
    }
}