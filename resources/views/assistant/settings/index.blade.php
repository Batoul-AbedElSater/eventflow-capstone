@extends('layouts.assistant')

@section('content')
<div class="settings-wrapper">
    <nav class="settings-navbar">
        <div class="settings-header">
            <h1 class="settings-title" style="color: white;">Assistant Settings</h1>
            <p class="settings-subtitle">Manage your profile, skills, and preferences</p>
        </div>
    </nav>

    <div class="settings-container">
        <!-- Left Sidebar Navigation -->
        <div class="settings-sidebar">
            <div class="sidebar-menu">
                <a href="{{ route('assistant.settings.account') }}" class="menu-item {{ request()->routeIs('assistant.settings.account') ? 'active' : '' }}">
                    <i class="fas fa-user"></i> Account
                </a>
                <a href="{{ route('assistant.settings.skills') }}" class="menu-item {{ request()->routeIs('assistant.settings.skills') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i> Skills
                </a>
                <a href="{{ route('assistant.settings.availability') }}" class="menu-item {{ request()->routeIs('assistant.settings.availability') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> Availability
                </a>
                <a href="{{ route('assistant.settings.notifications') }}" class="menu-item {{ request()->routeIs('assistant.settings.notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                <a href="{{ route('assistant.settings.appearance') }}" class="menu-item {{ request()->routeIs('assistant.settings.appearance') ? 'active' : '' }}">
                    <i class="fas fa-palette"></i> Appearance
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="settings-content">
            @isset($stats)
                <!-- Performance Stats -->
                <div class="stats-grid">
                    <div class="stat-card" style="border-top: 4px solid var(--coral-haze);">
                        <h4>Completed Tasks</h4>
                        <p class="stat-number" style="color: var(--coral-haze);">{{ $stats['completed_tasks'] }}</p>
                        <span class="stat-label">All time</span>
                    </div>
                    <div class="stat-card" style="border-top: 4px solid var(--calypso-berry);">
                        <h4>In Progress</h4>
                        <p class="stat-number" style="color: var(--calypso-berry);">{{ $stats['in_progress_tasks'] }}</p>
                        <span class="stat-label">Current</span>
                    </div>
                    <div class="stat-card" style="border-top: 4px solid var(--garden-green);">
                        <h4>Orders Placed</h4>
                        <p class="stat-number" style="color: var(--garden-green);">{{ $stats['orders_placed'] }}</p>
                        <span class="stat-label">All time</span>
                    </div>
                    <div class="stat-card" style="border-top: 4px solid var(--vampire-hunter);">
                        <h4>Rating</h4>
                        <p class="stat-number" style="color: var(--vampire-hunter);">{{ $stats['ratings'] }}</p>
                        <span class="stat-label">/ 5.0</span>
                    </div>
                </div>
            @endisset

            <div class="unified-settings">
                <h2 style="color: #475B35; margin-bottom: 12px;">General Settings</h2>

                <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:18px;">
                    <div>
                        <h4 style="margin:0 0 6px 0;">In-app Notifications</h4>
                        <p style="margin:0;color:#666;">When turned off, in-app notifications are suppressed — messages are still saved and available in Messages.</p>
                    </div>
                    <div>
                        <input type="checkbox" id="in_app_notifications" style="width:36px;height:20px;" {{ (isset($preferences) && isset($preferences->in_app_notifications)) ? ($preferences->in_app_notifications ? 'checked' : '') : 'checked' }} />
                    </div>
                </div>

                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;">
                    <button id="exportDataBtn" class="menu-item" style="padding:12px 18px;">⬇ Download my data</button>

                    <form id="logoutForm" method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="menu-item" style="padding:12px 18px;">🔒 Logout</button>
                    </form>

                    <button id="deleteAccountBtn" class="menu-item delete-account" style="padding:12px 18px;">🗑 Delete account</button>
                </div>

                <script>
                    (function(){
                        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';

                        document.getElementById('in_app_notifications').addEventListener('change', async function(){
                            const enabled = this.checked ? 1 : 0;
                            try{
                                const res = await fetch('{{ route("assistant.settings.notifications.update") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf
                                    },
                                    body: JSON.stringify({ in_app_notifications: enabled })
                                });
                                const data = await res.json();
                                if (!res.ok) throw new Error(data.message || 'Failed');
                                const el = document.createElement('div');
                                el.textContent = data.message || 'Saved';
                                el.style.position = 'fixed'; el.style.bottom = '24px'; el.style.right = '24px'; el.style.background = '#fff'; el.style.padding = '10px 14px'; el.style.border = '1px solid #ddd'; el.style.borderRadius = '8px';
                                document.body.appendChild(el);
                                setTimeout(()=>el.remove(),2200);
                            }catch(e){
                                alert('Could not save preference: ' + (e.message||e));
                            }
                        });

                        document.getElementById('exportDataBtn').addEventListener('click', function(){
                            window.location.href = '{{ route("assistant.settings.export") }}';
                        });

                        document.getElementById('deleteAccountBtn').addEventListener('click', function(){
                            if (!confirm('Are you sure you want to permanently delete your account? This cannot be undone.')) return;

                            const passwd = prompt('To confirm account deletion please enter your password:');
                            if (!passwd) { alert('Password required to delete account'); return; }

                            fetch('{{ route("assistant.settings.delete") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrf
                                },
                                body: JSON.stringify({ confirmation: true, password: passwd })
                            }).then(r=>r.json()).then(j=>{
                                if (j.success) window.location.href = '{{ route("home") }}'; else alert(j.message||'Failed');
                            }).catch(e=>alert('Failed to delete account'));
                        });
                    })();
                </script>
            </div>
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
        }
        .menu-item {
            white-space: nowrap;
            flex: 0 0 auto;
        }
    }
</style>
@endsection