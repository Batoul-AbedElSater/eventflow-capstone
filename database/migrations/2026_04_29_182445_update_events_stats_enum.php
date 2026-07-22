<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip for SQLite - not supported
        if (DB::getDriverName() !== 'sqlite') {
            // Update status enum to include all needed values
            DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft', 'pending', 'confirmed', 'declined', 'planned', 'in_progress', 'completed', 'cancelled') DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        // Skip for SQLite - not supported
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('draft', 'planned', 'in_progress', 'cancelled', 'completed') DEFAULT 'draft'");
        }
    }
};