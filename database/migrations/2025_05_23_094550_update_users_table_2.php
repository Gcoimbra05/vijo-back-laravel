<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('reminders')->default(false)->after('guided_tours');
            $table->boolean('notifications')->default(false)->after('reminders');
            $table->string('timezone', 100)->nullable()->after('notifications');
            $table->boolean('optInNewsUpdates')->default(false)->after('timezone');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['reminders', 'notifications', 'timezone', 'optInNewsUpdates']);
        });
    }
};