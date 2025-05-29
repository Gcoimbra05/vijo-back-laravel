<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->integer('id', true, true);
            $table->string('name', 250)->nullable();
            $table->string('description', 250)->nullable();
            $table->tinyInteger('payment_mode')->default(1)->comment('1: One Time, 2: Recurring');
            $table->double('monthly_cost')->default(0);
            $table->double('annual_cost')->default(0);
            $table->string('payment_link', 255)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0: Deactivated, 1: Active, 2: Deleted')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('membership_plans');
    }
}