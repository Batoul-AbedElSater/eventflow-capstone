<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_budget_drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('planner_id')->constrained('users')->cascadeOnDelete();
            $table->json('ai_response');
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->unique(['event_id', 'planner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_budget_drafts');
    }
};