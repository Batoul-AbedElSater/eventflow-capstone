@extends('layouts.assistant')

@section('title', 'Assistant Dashboard')
@section('page-title', 'Welcome Back!')

@section('content')
<div style="max-width: 1200px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
        <h2 style="font-size: 32px; margin-bottom: 10px;">Welcome, {{ $user->name }}! 👋</h2>
        <p style="font-size: 16px; opacity: 0.9;">You're all set! Ready to assist with event planning.</p>
    </div>

    <div style="text-align: center; padding: 60px 20px;">
        <div style="margin-bottom: 30px;">
            <i class="fas fa-rocket" style="font-size: 80px; color: #667eea; opacity: 0.5;"></i>
        </div>

        <h3 style="font-size: 28px; color: #333; margin-bottom: 15px;">Coming Soon 🚀</h3>
        <p style="font-size: 16px; color: #666; margin-bottom: 40px; max-width: 500px; margin-left: auto; margin-right: auto;">
            Exciting new features are on the way! We're preparing task management and collaboration tools to help you work more efficiently with your planning team.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; max-width: 800px; margin: 0 auto;">
            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-tasks" style="font-size: 40px; color: #667eea; margin-bottom: 10px;"></i>
                <h4 style="color: #333; margin-bottom: 8px;">Task Management</h4>
                <p style="color: #666; font-size: 14px;">Manage assigned tasks and track progress</p>
            </div>

            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-bell" style="font-size: 40px; color: #667eea; margin-bottom: 10px;"></i>
                <h4 style="color: #333; margin-bottom: 8px;">Notifications</h4>
                <p style="color: #666; font-size: 14px;">Stay updated with real-time notifications</p>
            </div>

            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-comments" style="font-size: 40px; color: #667eea; margin-bottom: 10px;"></i>
                <h4 style="color: #333; margin-bottom: 8px;">Messaging</h4>
                <p style="color: #666; font-size: 14px;">Communicate with your planning team</p>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: #f8f9fa;
    }
</style>
@endsection
