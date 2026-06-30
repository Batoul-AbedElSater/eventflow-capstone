@extends('layouts.client')

@section('content')
<div class="settings-wrapper">
    <!-- Luxury Navigation Bar -->
    <nav class="settings-navbar">
        <div class="settings-header">
            <h1 class="settings-title" style="color: #475B35;">Settings & Preferences</h1>
            <p class="settings-subtitle">Manage your account and customize your experience</p>
        </div>
    </nav>

    <div class="settings-container">
        <!-- Left Sidebar Navigation -->
        <div class="settings-sidebar">
            <div class="sidebar-menu">
                <a href="{{ route('client.settings.account') }}" class="menu-item active">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Account</span>
                </a>

                <a href="{{ route('client.settings.notifications') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>Notifications</span>
                </a>

                <a href="{{ route('client.settings.privacy') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Privacy</span>
                </a>

                <a href="{{ route('client.settings.appearance') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    <span>Appearance</span>
                </a>

                <a href="{{ route('client.settings.preferences') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span>Preferences</span>
                </a>

                <div class="sidebar-divider"></div>

                <a href="{{ route('client.settings.export') }}" class="menu-item">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-4m0 0L7 9m5-5l5 5"></path>
                    </svg>
                    <span>Download Data</span>
                </a>

                <button class="menu-item delete-account" data-action="deleteAccount">
                    <svg class="menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Delete Account</span>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="settings-content">
            @yield('settings-content')
        </div>
    </div>
</div>

<style>
    :root {
        --garden-green: #475B35;
        --amnesiac-white: #F5F9E5;
        --coral-haze: #E19184;
        --calypso-berry: #C63E4E;
        --vampire-hunter: #620607;
        --cream: #EFE7DA;
    }

    .settings-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--amnesiac-white) 0%, var(--cream) 100%);
    }

    .settings-navbar {
        background: var(--coral-haze);
        color: white;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(71, 91, 53, 0.15);
    }

    .settings-header {
        max-width: 1400px;
        margin: 0 auto;
    }

    .settings-title {
        font-family: 'Playfair Display', serif;
        font-size: 48px;
        font-weight: 900;
        margin-bottom: 8px;
        letter-spacing: -1px;
    }

    .settings-subtitle {
        font-size: 16px;
        opacity: 0.9;
    }

    .settings-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px;
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 40px;
    }

    .settings-sidebar {
        position: sticky;
        top: 100px;
        height: fit-content;
    }

    .sidebar-menu {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(71, 91, 53, 0.08);
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        color: #555;
        text-decoration: none;
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        background: none;
        width: 100%;
        font-size: 14px;
        font-weight: 500;
    }

    .menu-item:hover {
        background: var(--amnesiac-white);
        color: var(--calypso-berry);
        border-left-color: var(--coral-haze);
    }

    .menu-item.active {
        background: linear-gradient(90deg, var(--coral-haze) 0%, var(--calypso-berry) 100%);
        color: white;
        border-left-color: white;
    }

    .menu-icon {
        width: 20px;
        height: 20px;
    }

    .sidebar-divider {
        height: 1px;
        background: #eee;
        margin: 8px 0;
    }

    .delete-account {
        color: var(--vampire-hunter);
    }

    .delete-account:hover {
        background: #fee;
    }

    .settings-content {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(71, 91, 53, 0.08);
    }

    @media (max-width: 1024px) {
        .settings-container {
            grid-template-columns: 1fr;
        }

        .settings-sidebar {
            position: static;
        }

        .sidebar-menu {
            display: flex;
            overflow-x: auto;
            border-radius: 12px;
        }

        .menu-item {
            white-space: nowrap;
            flex: 0 0 auto;
        }
    }
</style>
@endsection