@extends('layouts.planner')

@section('content')
<div class="settings-wrapper">
    <nav class="settings-navbar">
        <div class="settings-header">
            <h1 class="settings-title" style="color: white;">Planner Settings & Dashboard</h1>
            <p class="settings-subtitle">Manage your business and grow</p>
        </div>
    </nav>

    <div class="settings-container">
        <!-- Left Sidebar Navigation -->
        <div class="settings-sidebar">
            <div class="sidebar-menu">
                <a href="{{ route('planner.settings.account') }}" class="menu-item {{ request()->routeIs('planner.settings.account') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> Account
                </a>
                <a href="{{ route('planner.settings.business') }}" class="menu-item {{ request()->routeIs('planner.settings.business') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Business
                </a>
                <a href="{{ route('planner.settings.team') }}" class="menu-item {{ request()->routeIs('planner.settings.team') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Team
                </a>
                <a href="{{ route('planner.settings.vendors') }}" class="menu-item {{ request()->routeIs('planner.settings.vendors') ? 'active' : '' }}">
                    <i class="fas fa-store"></i> Vendors
                </a>
                <a href="{{ route('planner.settings.analytics') }}" class="menu-item {{ request()->routeIs('planner.settings.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Analytics
                </a>
                <a href="{{ route('planner.settings.notifications') }}" class="menu-item {{ request()->routeIs('planner.settings.notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                <a href="{{ route('planner.settings.appearance') }}" class="menu-item {{ request()->routeIs('planner.settings.appearance') ? 'active' : '' }}">
                    <i class="fas fa-palette"></i> Appearance
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="settings-content">
            @if(isset($businessStats))
                <!-- Business Stats -->
                <div class="stats-grid">
                    <div class="stat-card" style="border-top: 4px solid var(--coral-haze);">
                        <h4>Total Events</h4>
                        <p class="stat-number" style="color: var(--coral-haze);">{{ $businessStats['total_events'] }}</p>
                        <span class="stat-label">All time</span>
                    </div>

                    <div class="stat-card" style="border-top: 4px solid var(--calypso-berry);">
                        <h4>Active Clients</h4>
                        <p class="stat-number" style="color: var(--calypso-berry);">{{ $businessStats['active_clients'] }}</p>
                        <span class="stat-label">Current</span>
                    </div>

                    <div class="stat-card" style="border-top: 4px solid var(--garden-green);">
                        <h4>Team Members</h4>
                        <p class="stat-number" style="color: var(--garden-green);">{{ $businessStats['team_members'] }}</p>
                        <span class="stat-label">Assistants</span>
                    </div>

                    <div class="stat-card" style="border-top: 4px solid var(--vampire-hunter);">
                        <h4>Total Revenue</h4>
                        <p class="stat-number" style="color: var(--vampire-hunter);">${{ number_format($businessStats['total_revenue'], 0) }}</p>
                        <span class="stat-label">All time</span>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions" style="margin-top: 32px;">
                    <h3 style="color: #475B35; margin-bottom: 16px;">Quick Actions</h3>
                    <div class="action-buttons">
                        <a href="{{ route('planner.settings.business') }}" class="action-btn">
                            <span class="action-icon">📊</span>
                            Update Business Info
                        </a>
                        <a href="{{ route('planner.settings.team') }}" class="action-btn">
                            <span class="action-icon">👥</span>
                            Manage Team
                        </a>
                        <a href="{{ route('planner.settings.vendors') }}" class="action-btn">
                            <span class="action-icon">⭐</span>
                            Favorite Vendors
                        </a>
                        <a href="{{ route('planner.settings.analytics') }}" class="action-btn">
                            <span class="action-icon">📈</span>
                            View Analytics
                        </a>
                    </div>
                </div>
            @endif

            <!-- This is where the child view (account, business, etc.) will render -->
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

    .menu-item i {
        width: 20px;
        text-align: center;
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

    .settings-content {
        background: white;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(71, 91, 53, 0.08);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(245,249,229,0.5) 0%, rgba(239,231,218,0.5) 100%);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
    }

    .stat-card h4 {
        font-size: 12px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    .stat-number {
        font-family: 'Playfair Display', serif;
        font-size: 36px;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 12px;
        color: #999;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, rgba(225, 145, 132, 0.1) 0%, rgba(198, 62, 78, 0.1) 100%);
        border: 2px solid transparent;
        border-radius: 8px;
        text-decoration: none;
        color: #333;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        border-color: var(--coral-haze);
        background: linear-gradient(135deg, rgba(225, 145, 132, 0.2) 0%, rgba(198, 62, 78, 0.2) 100%);
        transform: translateY(-2px);
    }

    .action-icon {
        font-size: 24px;
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