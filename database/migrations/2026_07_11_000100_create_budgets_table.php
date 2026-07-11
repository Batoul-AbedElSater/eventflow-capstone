<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('planner_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_client_budget', 10, 2)->nullable();
            $table->decimal('planner_fee', 10, 2)->nullable();
            $table->decimal('total_assistant_fees', 10, 2)->nullable();
            $table->decimal('estimated_total', 10, 2)->nullable();
            $table->decimal('actual_total', 10, 2)->nullable();
            $table->string('status')->default('draft');
            $table->boolean('shared_with_client')->default(false);
            $table->text('planner_notes')->nullable();
            $table->timestamps();

            $table->unique('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
