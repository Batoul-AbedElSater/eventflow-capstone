n<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'task', 'event', 'request', 'message', 'weather', 'conflict', 'health'
            $table->string('priority'); // 'low', 'medium', 'high', 'urgent'
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->string('action_url')->nullable(); // Where to go when clicked
            $table->boolean('is_read')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
