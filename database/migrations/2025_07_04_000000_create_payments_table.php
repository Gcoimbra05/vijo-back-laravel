<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('subscription_id')->nullable();
            $table->string('customerID')->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->tinyInteger('status')->default(1)->comment('1: paid, 2: failed, 3: refunded');
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};