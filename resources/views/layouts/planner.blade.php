<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - EventFlow Planner</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/planner-dashboard.css') }}">

    <style>
    /* ===== COLOR PALETTE ===== */
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
    body {
    padding: 0 35px;
}
    /* sidebar */
    aside.planner-sidebar {
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
    color: var(--cream);
}


.sidebar-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 28px;
    text-decoration: none;
    color: var(--vampire);
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 600;
    border-radius: 0 50px 50px 0;
    margin-right: 20px;
    transition: background 0.22s ease, color 0.22s ease, transform 0.22s ease;
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
    box-shadow: var(--coral);
}

.sidebar-link.active i {
    color: white;
}

    /* ==================== HEADER (NAVBAR) – CLIENT STYLE, NO MOOD, NO SEARCH ==================== */
    .header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 75px;

    border-bottom: 3px solid transparent;
    border-image: linear-gradient(90deg, var(--coral), var(--berry), var(--vampire)) 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 50px;
    z-index: 1000;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
}

.header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}




.header-center {
    display: none;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}
    /* ===== VOICE COMMANDER BUTTON (circle, green border, only icon) ===== */
    .voice-commander-btn {
        background: white;
        border: 3px solid var(--green);
        cursor: pointer;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .voice-commander-btn i {
        font-size: 20px;
        color: var(--green);
        transition: all 0.3s;
    }
    .voice-commander-btn:hover {
        transform: translateY(-2px);
        border-color: var(--green);
        box-shadow: 0 8px 20px rgba(71,91,53,0.4);
    }
    .voice-commander-btn:hover i {
        color: var(--green);
        animation: micPulse 0.8s ease-in-out;
    }
    @keyframes micPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }

    /* ===== NOTIFICATIONS BELL (vampire border) ===== */
    .notifications {
        position: relative;
        cursor: pointer;
        background: white;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border: 3px solid var(--vampire);
    }
    .notifications:hover {
        transform: translateY(-2px) rotate(8deg);
        border-color: var(--vampire);
        box-shadow: 0 8px 20px rgba(98,6,7,0.35);
    }
    .notifications i {
        font-size: 20px;
        color: var(--green);
        transition: all 0.3s;
    }
    .notifications:hover i {
        color: var(--vampire);
        animation: bellRing 0.5s ease-in-out;
    }
    @keyframes bellRing {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-12deg); }
        75% { transform: rotate(12deg); }
    }
    .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: linear-gradient(135deg, var(--berry), var(--vampire));
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 11px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(98,6,7,0.3);
        font-family: 'Raleway', sans-serif;
        animation: badgePulse 2s ease-in-out infinite;
    }
    @keyframes badgePulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* ===== PROFILE DROPDOWN (click toggle, berry border) ===== */
    .profile-dropdown {
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 6px 18px 6px 8px;
        border-radius: 50px;
        background: white;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        border: 3px solid var(--berry);
    }
    .profile-dropdown:hover {
        transform: translateY(-2px);
        border-color: var(--berry);
        box-shadow: 0 10px 24px rgba(198,62,78,0.35);
    }
    .profile-dropdown img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--green);
        transition: all 0.3s;
    }
    .profile-dropdown:hover img {
        border-color: var(--vampire);
        transform: scale(1.05);
    }
    .profile-dropdown span {
        font-family: 'Raleway', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--green);
        transition: all 0.3s;
    }
    .profile-dropdown:hover span {
        color: var(--berry);
    }
    .profile-dropdown i {
        font-size: 13px;
        color: var(--green);
        transition: transform 0.3s ease;
    }
    .profile-dropdown:hover i {
        color: var(--berry);
    }
    .profile-dropdown.open i {
        transform: rotate(180deg);
        color: var(--berry);
    }

    /* ===== DROPDOWN MENU (berry border) ===== */
    .dropdown-menu {
        position: absolute;
        top: 62px;
        right: 0;
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        min-width: 240px;
        display: none;
        z-index: 1000;
        border: 3px solid var(--berry);
        overflow: hidden;
        animation: dropdownSlide 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .dropdown-menu.show {
        display: block;
    }
    @keyframes dropdownSlide {
        from { opacity: 0; transform: translateY(-15px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .dropdown-menu a,
    .dropdown-menu button {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 22px;
        color: var(--green);
        text-decoration: none;
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        cursor: pointer;
        font-family: 'Raleway', sans-serif;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.25s;
    }
    .dropdown-menu a i,
    .dropdown-menu button i {
        font-size: 16px;
        width: 22px;
        text-align: center;
        transition: all 0.25s;
    }
    .dropdown-menu a:hover,
    .dropdown-menu button:hover {
        background: linear-gradient(135deg, var(--coral), var(--berry));
        color: white;
        transform: translateX(6px);
    }
    .dropdown-menu hr {
        margin: 6px 0;
        border: none;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--cream), transparent);
    }

    /* ==================== MAIN CONTENT ==================== */
    .main-content {
      /*  margin-left: 280px;
        margin-top: 75px;
        min-height: calc(100vh - 70px);
        padding: 30px;*/

          margin-left: 240px;
    margin-top: 75px;
    min-height: calc(100vh - 75px);
    padding: 30px;
    }

    /* ==================== MODALS (unchanged) ==================== */
    .mood-modal,
    .voice-commander-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }
    .mood-modal.active,
    .voice-commander-modal.active {
        display: flex;
    }
    .mood-modal-overlay,
    .voice-modal-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
    }
    .mood-modal-content,
    .voice-modal-content {
        position: relative;
        background: white;
        border-radius: 30px;
        max-width: 90%;
        width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 0;
        z-index: 1;
    }

    /* ===== RAPID FIRE MODE STYLES & KEYFRAMES ===== */
@keyframes slideInRight {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(400px); opacity: 0; }
}
@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.8); }
}

.rapid-fire-card .rapid-fire-container {
    padding: 20px;
}
.rapid-fire-task {
    background: linear-gradient(135deg, #F5F9E5, #FFF);
    padding: 25px;
    border-radius: 20px;
    margin-bottom: 20px;
}
.task-counter {
    font-size: 12px;
    color: #C63E4E;
    font-weight: 800;
    margin-bottom: 10px;
    text-transform: uppercase;
}
.rapid-fire-task h4 {
    font-size: 20px;
    color: #475B35;
    margin-bottom: 8px;
}
.task-event-name {
    font-size: 14px;
    color: #7F8C8D;
    margin-bottom: 20px;
}
.rapid-fire-actions {
    display: flex;
    gap: 12px;
}
.rapid-btn {
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    border: none;
}
.rapid-btn.done {
    background: #7ED321;
    color: white;
}
.rapid-btn.skip {
    background: #F5A623;
    color: white;
}
.rapid-btn.remind {
    background: #4A90E2;
    color: white;
}
.rapid-btn:hover {
    transform: translateY(-2px);
}
.rapid-fire-progress {
    margin-top: 20px;
}
.progress-bar-rapid {
    background: #e0e0e0;
    border-radius: 10px;
    height: 10px;
    overflow: hidden;
}
.progress-fill-rapid {
    background: linear-gradient(90deg, #E19184, #C63E4E);
    width: 0%;
    height: 100%;
    transition: width 0.3s;
}
.progress-text {
    font-size: 13px;
    margin-top: 8px;
    display: block;
}
.rapid-fire-streak {
    margin-top: 15px;
    padding: 10px;
    background: #FFF0E6;
    border-radius: 12px;
    text-align: center;
    color: #E19184;
    font-weight: 700;
}
.completed-all {
    text-align: center;
    padding: 40px;
}
.completed-all i {
    font-size: 50px;
    color: #FFD700;
    margin-bottom: 15px;
}

/* Voice Commander modal theme - paste after the existing modal CSS */
.voice-commander-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 10000;
    align-items: center;
    justify-content: center;
}

.voice-commander-modal.active {
    display: flex;
}

.voice-commander-modal .voice-modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(6px);
}

.voice-commander-modal .voice-modal-content {
    position: relative;
    z-index: 1;
    width: min(90%, 500px) !important;
    max-width: 500px !important;
    max-height: 90vh;
    overflow-y: auto;
    padding: 40px !important;
    background: var(--cream) !important;
    border-radius: 30px !important;
    box-shadow: 0 30px 100px rgba(0, 0, 0, 0.6);
    animation: modalSlideUp 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes modalSlideUp {
    from {
        transform: translateY(100px) scale(0.8);
        opacity: 0;
    }
    to {
        transform: translateY(0) scale(1);
        opacity: 1;
    }
}

.voice-close-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--white);
    border: 3px solid var(--berry);
    color: var(--coral);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.3s;
}

.voice-close-btn:hover {
    background: var(--berry);
    color: var(--white);
    transform: rotate(90deg);
}

.voice-header {
    text-align: center;
    margin-bottom: 30px;
}

.voice-icon-pulse {
    width: 120px;
    height: 120px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: var(--berry);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 50px;
    color: var(--white);
    position: relative;
    box-shadow: 0 15px 50px rgba(225, 145, 132, 0.6);
}

.voice-icon-pulse i {
    color: var(--white);
    position: relative;
    z-index: 2;
}

.voice-icon-pulse.listening {
    animation: voicePulse 1.5s ease-in-out infinite;
}

@keyframes voicePulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 15px 50px rgba(225, 145, 132, 0.6);
    }
    50% {
        transform: scale(1.15);
        box-shadow: 0 20px 70px rgba(225, 145, 132, 0.9);
    }
}

.pulse-ring {
    position: absolute;
    inset: -1px ;
    border-radius: 60%;
    border: 4px solid var(--coral);
    opacity: 0;
}

.voice-icon-pulse.listening .pulse-ring {
    animation: pulsateRing 2s ease-out infinite;
}

.voice-icon-pulse.listening .pulse-ring:nth-child(2) {
    animation-delay: 1s;
}

@keyframes pulsateRing {
    0% {
        transform: scale(0.9);
        opacity: 0.7;
    }
    100% {
        transform: scale(1.4);
        opacity: 0;
    }
}

.voice-header h2 {
    margin: 0 0 12px;
    color: var(--vampire);
    font-family: 'DM Serif Display', 'Playfair Display', serif;
    font-size: 36px;
    font-weight: 800;
    line-height: 1.1;
}

#voiceStatus {
    margin: 0;
    color: var(--green);
    font-size: 18px;
    font-weight: 500;
}

.voice-suggestions {
    background: rgba(255, 255, 255, 0.36);
    border: 1px solid rgba(98, 6, 7, 0.08);
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 20px;
}

.suggestions-title {
    margin: 0 0 15px;
    color: var(--green);
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.suggestion-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.chip {
    padding: 10px 18px;
    background: var(--white);
    border: 2px solid var(--berry);
    border-radius: 25px;
    color: var(--vampire);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.chip:hover {
    background: var(--coral);
    color: var(--white);
}

.voice-transcript {
    min-height: 100px;
    background: var(--white);
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 20px;
    color: var(--green);
    font-size: 16px;
}

.transcript-final {
    color: var(--green);
    font-weight: 700;
    margin-bottom: 10px;
}

.transcript-interim {
    color: #9B9B9B;
    font-style: italic;
}

.btn-voice-toggle {
    width: 100%;
    padding: 20px;
    background: var(--berry);
    color: var(--white);
    border: none;
    border-radius: 30px;
    font-size: 20px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    box-shadow: 0 10px 30px rgba(225, 145, 132, 0.5);
}

.btn-voice-toggle i {
    color: var(--white);
}

.btn-voice-toggle:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(225, 145, 132, 0.7);
}

    </style>
    @stack('styles')
</head>
<body>

    <!-- Header -->
    <header class="header">
         <div class="header-left">

             <h1 style="font-family: 'Comic Sans MS', 'Raleway', sans-serif; font-size: 33px; font-weight: 900; margin: 0; display: flex; align-items: center; gap: 2px; cursor: pointer; transition: all 0.3s;">
                <span style="color:#620607;">Event</span><span style="color:#C63E4E;">Flow</span>
            </h1>

        </div>

        <div class="header-right">
<aside class="planner-sidebar">
    <a href="{{ route('planner.dashboard') }}" class="sidebar-link {{ request()->routeIs('planner.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="{{ route('planner.events.index') }}" class="sidebar-link {{ request()->routeIs('planner.events*') && !request()->routeIs('planner.events.analytics') ? 'active' : '' }}">
        <i class="fas fa-calendar-alt"></i> Events
    </a>
    <a href="{{ route('planner.requests') }}" class="sidebar-link {{ request()->routeIs('planner.requests*') ? 'active' : '' }}">
        <i class="fas fa-clipboard-list"></i> Requests
    </a>
    <a href="{{ route('planner.events.analytics') }}" class="sidebar-link {{ request()->routeIs('planner.events.analytics') ? 'active' : '' }}">
        <i class="fas fa-chart-line"></i> Analytics
    </a>
    <a href="{{ route('planner.tasks.index') }}" class="sidebar-link {{ request()->routeIs('planner.tasks*') ? 'active' : '' }}">
        <i class="fas fa-tasks"></i> Tasks
    </a>
    <a href="{{ route('planner.messages') }}" class="sidebar-link {{ request()->routeIs('planner.messages*') ? 'active' : '' }}">
        <i class="fas fa-envelope"></i> Messages
    </a>
    <!-- ✅ ADD/UPDATE THIS SETTINGS LINK -->
    <a href="{{ route('planner.settings.index') }}" class="sidebar-link {{ request()->routeIs('planner.settings*') ? 'active' : '' }}">
        <i class="fas fa-cog"></i> Settings
    </a>
     <a href="{{ route('planner.profile') }}" class="sidebar-link {{ request()->routeIs('profile*') ? 'active' : '' }}">
        <i class="fas fa-user"></i> Profile
    </a>
</aside>
            <!-- Voice Commander Button -->
            <button class="voice-commander-btn" id="voiceCommanderBtn"><i class="fas fa-microphone"></i></button>

            <!-- Notifications Bell -->
            <div class="notifications" id="notificationBellBtn">
                <i class="fas fa-bell"></i>
                <span class="badge" id="headerNotifBadge">0</span>
            </div>

            <!-- Profile Dropdown (click toggle) -->
           @php
    $avatar = Auth::user()->avatar_url
        ? asset('storage/' . Auth::user()->avatar_url)
        : asset('images/default-avatar.png');
@endphp

<div class="profile-dropdown" id="profileDropdownBtn">

    <!-- NAVBAR IMAGE -->
    <img src="{{ $avatar }}" alt="Profile">
    <span>{{ Auth::user()->name }}</span>
    <i class="fas fa-chevron-down"></i>

    <!-- DROPDOWN -->
    <div class="dropdown-menu" id="profileDropdownMenu">

        <!-- INSIDE MENU PROFILE (optional smaller preview) -->
        <div class="dropdown-profile">
            <img src="{{ $avatar }}" alt="Profile">
            <span>{{ Auth::user()->name }}</span>
        </div>

        <hr>

        <a href="{{ route('planner.profile') }}">
            <i class="fas fa-user"></i> My Profile
        </a>

        <a href="{{ route('planner.settings.index') }}">
            <i class="fas fa-cog"></i> Settings
        </a>

        <hr>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>

    </div>
</div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>
    <!-- ========== MODALS ========== -->

    <!-- Voice Commander Modal (full) -->
    <div class="voice-commander-modal" id="voiceCommanderModal">
        <div class="voice-modal-overlay"></div>
        <div class="voice-modal-content">
            <button class="voice-close-btn" id="voiceCloseBtn"><i class="fas fa-times"></i></button>
            <div class="voice-header">
               <div class="voice-icon-pulse listening" id="voiceIconPulse">
                    <div class="pulse-ring"></div><div class="pulse-ring"></div>
                    <i class="fas fa-microphone"></i>
                </div>
                <h2>Voice Commander</h2>
                <p id="voiceStatus">Click the microphone to start</p>
            </div>
            <div class="voice-suggestions">
                <p class="suggestions-title">Try saying:</p>
                <div class="suggestion-chips">
                    <span class="chip">"Show my events"</span>
                    <span class="chip">"Create new event"</span>
                    <span class="chip">"Go to dashboard"</span>
                    <span class="chip">"Show tasks"</span>
                </div>
            </div>
            <div class="voice-transcript" id="voiceTranscript"></div>
            <button class="btn-voice-toggle" id="voiceToggleBtn"><i class="fas fa-microphone"></i> Start Listening</button>
        </div>
    </div>

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
                <button class="notif-tab" data-filter="request"><i class="fas fa-calendar-plus"></i> Requests</button>
                <button class="notif-tab" data-filter="message"><i class="fas fa-envelope"></i> Messages</button>
                <button class="notif-tab" data-filter="task"><i class="fas fa-tasks"></i> Tasks</button>
                <button class="notif-tab" data-filter="urgent"><i class="fas fa-exclamation-triangle"></i> Urgent</button>
            </div>
            <div class="notif-list" id="notifModalList"></div>
            <div class="notif-modal-actions">
                <button class="notif-action-btn primary" id="modalMarkAllRead"><i class="fas fa-check-double"></i> Mark All Read</button>
                <button class="notif-action-btn secondary" id="modalClearAll"><i class="fas fa-trash-alt"></i> Clear All</button>
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="{{ asset('js/planner-dashboard.js') }}"></script>
    <script src="{{ asset('js/mood-voice-common.js') }}"></script>

    <!-- Dropdown chevron rotation (click toggle) -->
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
<script>
     // Check for expired timer on any planner page
            (function() {
                const endTime = localStorage.getItem('focusTimerEnd');
                const title = localStorage.getItem('focusTimerTitle');
                if (endTime && title && Date.now() >= parseInt(endTime)) {
                    localStorage.removeItem('focusTimerEnd');
                    localStorage.removeItem('focusTimerTitle');
                    alert(`🎉 Focus session "${title}" completed!`);
                }
            })();

</script>
</body>
</html>
