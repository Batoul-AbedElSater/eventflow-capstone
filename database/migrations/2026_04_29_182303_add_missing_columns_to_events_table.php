<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Add start_time and end_time
            if (!Schema::hasColumn('events', 'start_time')) {
                $table->time('start_time')->nullable()->after('start_date');
            }
            
            if (!Schema::hasColumn('events', 'end_time')) {
                $table->time('end_time')->nullable()->after('end_date');
            }
            
            // Add event_photo
            if (!Schema::hasColumn('events', 'event_photo')) {
                $table->string('event_photo')->nullable()->after('budget_overall');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'event_photo']);
        });
    }
};