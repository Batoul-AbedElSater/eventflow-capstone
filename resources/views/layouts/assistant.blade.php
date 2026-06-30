<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - EventFlow Assistant</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
    :root {
        --coral: #E19184;
        --berry: #C63E4E;
        --vampire: #620607;
        --cream: #EFE7DA;
        --white: #FFFFFF;
        --amnesiac: #F5F9E5;
        --green: #475B35;
        --green-dark: #2C3821;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Poppins', sans-serif;
        background: #f8f4ef;
        padding: 0 15px;
    }

    /* ===== HEADER ===== */
    .header {
        position: fixed;
        top: 0; left: 0; right: 0;
        height: 75px;
        background: var(--cream);
        border-bottom: 3px solid transparent;
        border-image: linear-gradient(90deg, var(--coral), var(--berry), var(--vampire)) 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 50px;
        z-index: 1000;
        box-shadow: 0 6px 24px rgba(0,0,0,0.06);
    }

    .header-left { display: flex; align-items: center; gap: 15px; }
    .header-right { display: flex; align-items: center; gap: 20px; }

    .logo-text {
        font-family: 'Comic Sans MS', 'Poppins', sans-serif;
        font-size: 33px;
        font-weight: 900;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 2px;
        cursor: pointer;
    }

    /* ===== SIDEBAR ===== */
    aside.assistant-sidebar {
        position: fixed;
        left: 0;
        top: 75px;
        width: 270px;
        height: calc(100vh - 75px);
        z-index: 100;
        background: var(--cream);
        box-shadow: 4px 0 18px rgba(98, 6, 7, 0.06);
        display: flex;
        flex-direction: column;
        padding: 32px 0;
        gap: 6px;
    }

    .sidebar-section-label {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 700;
        color: var(--berry);
        letter-spacing: 0.12em;
        text-transform: uppercase;
        padding: 18px 28px 6px;
        opacity: 0.7;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 28px;
        text-decoration: none;
        color: var(--vampire);
        font-family: 'Poppins', sans-serif;
        font-size: 15px;
        font-weight: 600;
        border-radius: 0 50px 50px 0;
        margin-right: 20px;
        transition: background 0.22s ease, color 0.22s ease, transform 0.22s ease;
        position: relative;
    }

    .sidebar-link i {
        width: 22px;
        font-size: 1.1rem;
        text-align: center;
        flex-shrink: 0;
    }

    .sidebar-link:hover {
        background: rgba(198, 62, 78, 0.08);
        color: var(--berry);
        transform: translateX(4px);
    }

    .sidebar-link.active {
        background: var(--coral);
        color: white;
        box-shadow: 0 4px 12px rgba(225, 145, 132, 0.4);
    }

    .sidebar-link.active i { color: white; }

    .sidebar-badge {
        margin-left: auto;
        background: var(--berry);
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
        min-width: 22px;
        text-align: center;
    }

    /* ===== NOTIFICATIONS BELL ===== */
    .notifications {
        position: relative;
        cursor: pointer;
        background: white;
        width: 48px; height: 48px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border: 3px solid var(--vampire);
    }
    .notifications:hover {
        transform: translateY(-2px) rotate(8deg);
        box-shadow: 0 8px 20px rgba(98,6,7,0.35);
    }
    .notifications i { font-size: 20px; color: var(--green); }
    .badge {
        position: absolute;
        top: -5px; right: -5px;
        background: linear-gradient(135deg, var(--berry), var(--vampire));
        color: white;
        border-radius: 50%;
        width: 22px; height: 22px;
        font-size: 11px; font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(98,6,7,0.3);
    }

    /* ===== PROFILE DROPDOWN ===== */
    .profile-dropdown {
        position: relative;
        cursor: pointer;
        display: flex; align-items: center; gap: 12px;
        padding: 6px 18px 6px 8px;
        border-radius: 50px;
        background: white;
        transition: all 0.4s cubic-bezier(0.34,1.56,0.64,1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border: 3px solid var(--berry);
    }
    .profile-dropdown:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(198,62,78,0.35);
    }
    .profile-dropdown img {
        width: 42px; height: 42px;
        border-radius: 50%; object-fit: cover;
        border: 3px solid var(--green);
    }
    .profile-dropdown span {
        font-size: 15px; font-weight: 700;
        color: var(--green);
    }
    .profile-dropdown:hover span { color: var(--berry); }
    .profile-dropdown i { font-size: 13px; color: var(--green); transition: transform 0.3s ease; }
    .profile-dropdown.open i { transform: rotate(180deg); color: var(--berry); }

    .dropdown-menu {
        position: absolute;
        top: 62px; right: 0;
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        min-width: 240px;
        display: none;
        z-index: 1000;
        border: 3px solid var(--berry);
        overflow: hidden;
        animation: dropdownSlide 0.25s cubic-bezier(0.34,1.56,0.64,1);
    }
    .dropdown-menu.show { display: block; }
    @keyframes dropdownSlide {
        from { opacity: 0; transform: translateY(-15px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .dropdown-menu a,
    .dropdown-menu button {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 22px;
        color: var(--green);
        text-decoration: none;
        width: 100%; text-align: left;
        background: none; border: none;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        font-size: 14px; font-weight: 600;
        transition: all 0.25s;
    }
    .dropdown-menu a i,
    .dropdown-menu button i { font-size: 16px; width: 22px; text-align: center; }
    .dropdown-menu a:hover,
    .dropdown-menu button:hover {
        background: linear-gradient(135deg, var(--coral), var(--berry));
        color: white;
        transform: translateX(6px);
    }
    .dropdown-menu hr {
        margin: 6px 0; border: none; height: 1px;
        background: linear-gradient(90deg, transparent, var(--cream), transparent);
    }

    /* ===== MAIN CONTENT ===== */
    .main-content {
        margin-left: 270px;
        margin-top: 75px;
        min-height: calc(100vh - 75px);
        padding: 30px;
    }

    /* ===== NOTIFICATION MODAL ===== */
    .notification-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }
    .notification-modal.active { display: flex; }
    .notification-modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(98, 6, 7, 0.85);
        backdrop-filter: blur(15px);
    }
    .notification-modal-content {
        background: linear-gradient(135deg, white, #EFE7DA);
        border-radius: 30px;
        padding: 40px;
        max-width: 900px;
        width: 95%;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
        position: relative;
        z-index: 1;
        animation: modalSlideUp 0.5s ease;
    }
    @keyframes modalSlideUp {
        from { transform: translateY(100px) scale(0.9); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    .notif-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: white;
        border: 2px solid #E19184;
        color: #620607;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.3s;
        z-index: 10;
    }
    .notif-close-btn:hover {
        background: #C63E4E;
        color: white;
        transform: rotate(90deg);
    }
    .notif-modal-header {
        text-align: center;
        margin-bottom: 35px;
    }
    .notif-header-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 15px;
        border-radius: 50%;
        background: linear-gradient(135deg, #E19184, #C63E4E);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: white;
    }
    .notif-modal-header h2 {
        font-size: 36px;
        font-weight: 700;
        color: #620607;
        margin-bottom: 8px;
    }
    .notif-subtitle {
        font-size: 15px;
        color: #7F8C8D;
    }
    .notif-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .notif-stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    .notif-stat-card .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
    }
    .notif-stat-card.total .stat-icon { background: linear-gradient(135deg, #4A90E2, #357ABD); }
    .notif-stat-card.unread .stat-icon { background: linear-gradient(135deg, #F5A623, #E68619); }
    .notif-stat-card.urgent .stat-icon { background: linear-gradient(135deg, #D0021B, #A00116); }
    .notif-stat-card .stat-info strong {
        display: block;
        font-size: 26px;
        color: #2C3E50;
        margin-bottom: 4px;
    }
    .notif-stat-card .stat-info span {
        font-size: 12px;
        color: #7F8C8D;
    }
    .notif-filter-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .notif-tab {
        padding: 10px 18px;
        background: white;
        border: 2px solid #EFE7DA;
        border-radius: 12px;
        color: #2C3E50;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }
    .notif-tab:hover {
        background: #EFE7DA;
        border-color: #E19184;
    }
    .notif-tab.active {
        background: linear-gradient(135deg, #E19184, #C63E4E);
        color: white;
        border-color: #C63E4E;
    }
    .notif-list {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 25px;
        padding-right: 10px;
    }
    .notif-list::-webkit-scrollbar {
        width: 8px;
    }
    .notif-list::-webkit-scrollbar-track {
        background: #F8F9FA;
    }
    .notif-list::-webkit-scrollbar-thumb {
        background: #E19184;
        border-radius: 4px;
    }
    .notif-list::-webkit-scrollbar-thumb:hover {
        background: #C63E4E;
    }
    .notif-modal-item {
        background: white;
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        transition: all 0.3s;
        border-left: 5px solid;
    }
    .notif-modal-item.unread {
        background: linear-gradient(135deg, rgba(225, 145, 132, 0.1), rgba(225, 145, 132, 0.05));
        border-left-color: #C63E4E;
    }
    .notif-modal-item.read {
        border-left-color: #95A5A6;
        opacity: 0.7;
    }
    .notif-modal-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .notif-item-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        flex-shrink: 0;
    }
    .notif-item-icon.notification-blue { background: linear-gradient(135deg, #4A90E2, #357ABD); }
    .notif-item-icon.notification-yellow { background: linear-gradient(135deg, #F5A623, #E68619); }
    .notif-item-icon.notification-orange { background: linear-gradient(135deg, #FF6B00, #E05500); }
    .notif-item-icon.notification-red { background: linear-gradient(135deg, #D0021B, #A00116); }
    .notif-item-content {
        flex: 1;
        min-width: 0;
    }
    .notif-item-content h4 {
        font-size: 15px;
        color: #2C3E50;
        margin-bottom: 4px;
        font-weight: 700;
    }
    .notif-item-content p {
        font-size: 13px;
        color: #7F8C8D;
        margin-bottom: 6px;
    }
    .notif-item-time {
        font-size: 11px;
        color: #95A5A6;
        font-style: italic;
    }
    .notif-item-actions {
        display: flex;
        gap: 8px;
    }
    .notif-item-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    .notif-item-btn.view {
        background: rgba(71, 91, 53, 0.1);
        color: #475B35;
    }
    .notif-item-btn.view:hover {
        background: #475B35;
        color: white;
    }
    .notif-item-btn.delete {
        background: rgba(208, 2, 27, 0.1);
        color: #D0021B;
    }
    .notif-item-btn.delete:hover {
        background: #D0021B;
        color: white;
    }
    .notif-empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .notif-empty-state i {
        font-size: 60px;
        color: #95A5A6;
        margin-bottom: 15px;
        opacity: 0.3;
    }
    .notif-empty-state p {
        font-size: 16px;
        color: #7F8C8D;
    }
    .notif-modal-actions {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    .notif-action-btn {
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 14px;
    }
    .notif-action-btn.primary {
        background: linear-gradient(135deg, #E19184, #C63E4E);
        color: white;
        border: none;
    }
    .notif-action-btn.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(225, 145, 132, 0.4);
    }
    .notif-action-btn.secondary {
        background: rgba(208, 2, 27, 0.1);
        color: #D0021B;
        border: 2px solid #D0021B;
    }
    .notif-action-btn.secondary:hover {
        background: #D0021B;
        color: white;
    }

    /* ===== VOICE COMMANDER MODAL ===== */
    .voice-commander-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }
    .voice-commander-modal.active { display: flex; }
    .voice-modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
    }
    .voice-modal-content {
        position: relative;
        background: white;
        border-radius: 30px;
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 1;
    }
    </style>

    @stack('styles')
</head>
<body>

<header class="header">
    <div class="header-left">
        <h1 class="logo-text">
            <span style="color:#620607;">Event</span><span style="color:#C63E4E;">Flow</span>
        </h1>
    </div>

    <div class="header-right">
        @php
            $unreadCount = \App\Models\Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->where('is_archived', false)
                ->count();
        @endphp

        <div class="notifications" id="notificationBellBtn" style="position: relative;">
            <i class="fas fa-bell"></i>
            <span class="badge" id="headerNotifBadge">{{ $unreadCount }}</span>
        </div>

        <div class="profile-dropdown" id="profileDropdownBtn">
         <img src="{{ Auth::user()->avatar_url }}" alt="Profile">            <span>{{ Auth::user()->name }}</span>
            <i class="fas fa-chevron-down"></i>
            <div class="dropdown-menu" id="profileDropdownMenu">
                <hr>
                <a href="#"><i class="fas fa-user"></i> My Profile</a>
                <a href="{{ route('assistant.settings.index') }}"><i class="fas fa-cog"></i> Settings</a>
                <hr>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

@php
    $pendingTasksCount = \App\Models\TaskAssignment::where('assistant_id', Auth::id())
        ->whereHas('task', function($q) {
            $q->where('status', '!=', 'done');
        })->count();
@endphp

<aside class="assistant-sidebar">
    <div class="sidebar-section-label">Main</div>

    <a href="{{ route('assistant.tasks') }}" class="sidebar-link {{ request()->routeIs('assistant.tasks*') ? 'active' : '' }}">
        <i class="fas fa-tasks"></i> My Tasks
        @if($pendingTasksCount > 0)
            <span class="sidebar-badge">{{ $pendingTasksCount }}</span>
        @endif
    </a>

    <a href="{{ route('assistant.orders') }}" class="sidebar-link {{ request()->routeIs('assistant.orders') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart"></i> My Orders
    </a>

    <div class="sidebar-section-label">Account</div>

    <a href="#" class="sidebar-link">
        <i class="fas fa-user"></i> Profile
    </a>

    <!-- ✅ ADD/UPDATE THIS SETTINGS LINK -->
    <a href="{{ route('assistant.settings.index') }}" class="sidebar-link {{ request()->routeIs('assistant.settings*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i> Settings
    </a>
</aside>

<main class="main-content">
    @yield('content')
</main>
<!-- ===== NOTIFICATION MODAL ===== -->
<div class="notification-modal" id="notificationModal">
    <div class="notification-modal-overlay"></div>
    <div class="notification-modal-content">
        <button class="notif-close-btn" id="notifCloseBtn"><i class="fas fa-times"></i></button>
        <div class="notif-modal-header">
            <div class="notif-header-icon"><i class="fas fa-bell"></i></div>
            <h2>Notification Center</h2>
            <p class="notif-subtitle">Stay on top of everything</p>
        </div>
        <div class="notif-stats-grid">
            <div class="notif-stat-card total"><div class="stat-icon"><i class="fas fa-bell"></i></div><div class="stat-info"><strong id="modalStatTotal">0</strong><span>Total Today</span></div></div>
            <div class="notif-stat-card unread"><div class="stat-icon"><i class="fas fa-envelope"></i></div><div class="stat-info"><strong id="modalStatUnread">0</strong><span>Unread</span></div></div>
            <div class="notif-stat-card urgent"><div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div><div class="stat-info"><strong id="modalStatUrgent">0</strong><span>Urgent</span></div></div>
        </div>
        <div class="notif-filter-tabs">
            <button class="notif-tab active" data-filter="all"><i class="fas fa-inbox"></i> All</button>
            <button class="notif-tab" data-filter="task"><i class="fas fa-tasks"></i> Tasks</button>
            <button class="notif-tab" data-filter="order"><i class="fas fa-shopping-cart"></i> Orders</button>
            <button class="notif-tab" data-filter="urgent"><i class="fas fa-exclamation-triangle"></i> Urgent</button>
        </div>
        <div class="notif-list" id="notifModalList"></div>
        <div class="notif-modal-actions">
            <button class="notif-action-btn primary" id="modalMarkAllRead"><i class="fas fa-check-double"></i> Mark All Read</button>
            <button class="notif-action-btn secondary" id="modalClearAll"><i class="fas fa-trash-alt"></i> Clear All</button>
        </div>
    </div>
</div>

<!-- ===== VOICE COMMANDER MODAL ===== -->
<div class="voice-commander-modal" id="voiceCommanderModal">
    <div class="voice-modal-overlay"></div>
    <div class="voice-modal-content">
        <button class="voice-close-btn" id="voiceCloseBtn"><i class="fas fa-times"></i></button>
        <div class="voice-header">
            <div class="voice-icon-pulse" id="voiceIconPulse">
                <div class="pulse-ring"></div><div class="pulse-ring"></div>
                <i class="fas fa-microphone"></i>
            </div>
            <h2>Voice Commander</h2>
            <p id="voiceStatus">Click the microphone to start</p>
        </div>
        <div class="voice-suggestions">
            <p class="suggestions-title">Try saying:</p>
            <div class="suggestion-chips">
                <span class="chip">"Show my tasks"</span>
                <span class="chip">"Show my orders"</span>
                <span class="chip">"Go to dashboard"</span>
            </div>
        </div>
        <div class="voice-transcript" id="voiceTranscript"></div>
        <button class="btn-voice-toggle" id="voiceToggleBtn"><i class="fas fa-microphone"></i> Start Listening</button>
    </div>
</div>

<script src="{{ asset('js/assistant-notification.js') }}"></script>
<script src="{{ asset('js/mood-voice-common.js') }}"></script>

<!-- Dropdown chevron rotation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownBtn = document.getElementById('profileDropdownBtn');
        const dropdownMenu = document.getElementById('profileDropdownMenu');
        if (dropdownBtn && dropdownMenu) {
            const chevron = dropdownBtn.querySelector('i');
            dropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = dropdownMenu.classList.toggle('show');
                dropdownBtn.classList.toggle('open');
                chevron.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
            });
            document.addEventListener('click', function(e) {
                if (!dropdownBtn.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                    dropdownBtn.classList.remove('open');
                    chevron.style.transform = 'rotate(0deg)';
                }
            });
        }
    });
</script>

@stack('scripts')
</body>
</html>