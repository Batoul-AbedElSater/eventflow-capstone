<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Notifications
            $table->boolean('email_event_updates')->default(true);
            $table->boolean('email_vendor_messages')->default(true);
            $table->boolean('email_planner_updates')->default(true);
            $table->boolean('email_new_inquiries')->default(true);
            $table->boolean('email_client_messages')->default(true);
            $table->boolean('email_assistant_updates')->default(true);
            $table->boolean('email_vendor_responses')->default(true);
            $table->boolean('email_reminders')->default(true);
            $table->boolean('email_newsletters')->default(false);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            $table->enum('notification_frequency', ['instant', 'daily', 'weekly'])->default('instant');
            
            // Appearance
            $table->enum('theme_mode', ['light', 'dark', 'auto'])->default('auto');
            $table->enum('color_scheme', ['coral', 'berry', 'green', 'mixed'])->default('mixed');
            $table->enum('font_size', ['small', 'medium', 'large'])->default('medium');
            $table->enum('dashboard_layout', ['grid', 'list', 'compact'])->nullable();
            $table->boolean('animations')->default(true);
            $table->enum('language', ['en', 'ar', 'fr'])->default('en');
            
            // Privacy
            $table->enum('profile_visibility', ['public', 'private', 'friends'])->default('private');
            $table->boolean('show_email')->default(false);
            $table->boolean('show_phone')->default(false);
            $table->boolean('allow_vendor_contact')->default(true);
            $table->boolean('allow_planner_suggestions')->default(true);
            $table->boolean('data_collection')->default(false);
            
            // Business (Planner)
            $table->string('company_name')->nullable();
            $table->enum('business_type', ['freelance', 'small_team', 'agency'])->nullable();
            $table->integer('years_experience')->nullable();
            $table->json('specializations')->nullable();
            $table->json('service_areas')->nullable();
            $table->string('business_license')->nullable();
            $table->string('tax_id')->nullable();
            
            // Skills (Assistant)
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert'])->nullable();
            $table->json('certifications')->nullable();
            $table->json('working_days')->nullable();
            $table->time('working_hours_start')->nullable();
            $table->time('working_hours_end')->nullable();
            $table->string('timezone')->nullable();
            $table->json('available_locations')->nullable();
            $table->boolean('remote_work')->default(false);
            
            // Preferences
            $table->json('favorite_vendors')->nullable();
            $table->json('blocked_vendors')->nullable();
            $table->decimal('vendor_rating_threshold', 3, 1)->nullable();
            $table->string('preferred_event_type')->nullable();
            $table->string('budget_range')->nullable();
            $table->integer('ideal_guest_count')->nullable();
            $table->string('portfolio_link')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};