<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('user_preferences', 'in_app_notifications')) {
                $table->boolean('in_app_notifications')->default(true)->after('push_notifications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            if (Schema::hasColumn('user_preferences', 'in_app_notifications')) {
                $table->dropColumn('in_app_notifications');
            }
        });
    }
};