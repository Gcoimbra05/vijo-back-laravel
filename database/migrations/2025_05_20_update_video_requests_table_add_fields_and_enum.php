<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->text('ref_note')->nullable()->after('ref_email');
            $table->unsignedInteger('contact_id')->nullable()->after('catalog_id');
            $table->unsignedInteger('group_id')->nullable()->after('contact_id');
            $table->dropColumn('status');

            $table->enum('status', [
                'Pending',
                'Accept',
                'Approved',
                'Reject',
                'Not Right Now',
                'Delete'
            ])->default('Pending')->after('ref_note');

            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('contact_groups')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('video_requests', function (Blueprint $table) {
            $table->dropColumn('ref_note');
            $table->dropColumn('contact_id');
            $table->dropColumn('group_id');
            $table->tinyInteger('status')->default(1)->comment('0: Canceled, 1: Active, 2: Denied, 3: Completed')->change();
        });
    }
};