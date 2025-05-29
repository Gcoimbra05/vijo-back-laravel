<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->after('ref_note');
            $table->string('tags', 255)->nullable()->after('title');
            $table->enum('type', ['daily', 'request'])->default('request')->after('tags');
        });
    }

    public function down()
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->dropColumn(['title', 'tags', 'type']);
        });
    }
};