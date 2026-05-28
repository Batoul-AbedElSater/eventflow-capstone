<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'deleted_by_sender')) {
                $table->boolean('deleted_by_sender')->default(false)->after('is_read');
            }
            if (!Schema::hasColumn('messages', 'deleted_by_receiver')) {
                $table->boolean('deleted_by_receiver')->default(false)->after('deleted_by_sender');
            }
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'deleted_by_sender')) {
                $table->dropColumn('deleted_by_sender');
            }
            if (Schema::hasColumn('messages', 'deleted_by_receiver')) {
                $table->dropColumn('deleted_by_receiver');
            }
        });
    }
};
