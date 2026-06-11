<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')
                  ->constrained('tasks')
                  ->onDelete('cascade');
            $table->foreignId('assistant_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->foreignId('assigned_by')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['task_id', 'assistant_id']);
            $table->index('assistant_id');
            $table->index('assigned_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};