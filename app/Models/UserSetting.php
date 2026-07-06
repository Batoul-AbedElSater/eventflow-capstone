<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        // Planner settings
        'company_name',
        'business_type',
        'years_experience',
        'specializations',
        'service_areas',
        'business_license',
        'tax_id',
        'vendor_rating_threshold',
        // Assistant settings
        'experience_level',
        'certifications',
        'working_days',
        'working_hours_start',
        'working_hours_end',
        'timezone',
        'available_locations',
        'remote_work',
        // Common
        'theme_mode',
        'color_scheme',
        'font_size',
        'animations',
        'language',
        'dashboard_layout',
        'profile_visibility',
        'show_email',
        'show_phone',
        'allow_vendor_contact',
        'allow_planner_suggestions',
        'data_collection',
        'push_notifications',
        'sms_notifications',
        'notification_frequency',
        'email_event_updates',
        'email_vendor_messages',
        'email_planner_updates',
        'email_reminders',
        'email_newsletters',
        'email_new_inquiries',
        'email_client_messages',
        'email_assistant_updates',
        'email_vendor_responses',
        'email_task_assignments',
        'email_task_reminders',
        'email_planner_messages',
    ];

    protected $casts = [
        'specializations' => 'array',
        'certifications' => 'array',
        'working_days' => 'array',
        'available_locations' => 'array',
        'service_areas' => 'array',
        'remote_work' => 'boolean',
        'animations' => 'boolean',
        'show_email' => 'boolean',
        'show_phone' => 'boolean',
        'allow_vendor_contact' => 'boolean',
        'allow_planner_suggestions' => 'boolean',
        'data_collection' => 'boolean',
        'push_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'email_event_updates' => 'boolean',
        'email_vendor_messages' => 'boolean',
        'email_planner_updates' => 'boolean',
        'email_reminders' => 'boolean',
        'email_newsletters' => 'boolean',
        'email_new_inquiries' => 'boolean',
        'email_client_messages' => 'boolean',
        'email_assistant_updates' => 'boolean',
        'email_vendor_responses' => 'boolean',
        'email_task_assignments' => 'boolean',
        'email_task_reminders' => 'boolean',
        'email_planner_messages' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
