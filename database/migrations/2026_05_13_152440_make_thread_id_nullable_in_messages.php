<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Make thread_id nullable (if it exists)
            if (Schema::hasColumn('messages', 'thread_id')) {
                $table->unsignedBigInteger('thread_id')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'thread_id')) {
                $table->unsignedBigInteger('thread_id')->nullable(false)->change();
            }
        });
    }
};