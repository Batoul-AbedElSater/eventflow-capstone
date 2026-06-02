<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
       if (!Schema::hasTable('vendors')) {
            Schema::create('vendors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category');
                $table->string('email')->nullable();
                $table->string('phoneNumber');
                $table->string('website')->nullable();
                $table->decimal('rating',3,2)->default(0);
                $table->string('imageIcon');
                $table->text('description')->nullable();
                $table->json('locations');
                $table->string('instagram');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
