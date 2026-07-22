<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip for SQLite - not supported
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo','in_progress','completed','pending','done') NOT NULL DEFAULT 'todo'");
        }
    }

    public function down(): void
    {
        // Skip for SQLite - not supported
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo','in_progress','completed') NOT NULL DEFAULT 'todo'");
        }
    }
};
