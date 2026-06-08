<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - EventFlow Client</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/client-variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-luxury.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-event-show.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-forms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">

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

 aside.client-sidebar {
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

    /* ==================== HEADER (NAVBAR) – NO SEARCH, NO MOOD ==================== */
   /* ===== LUXURY FONTS ===== */
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Raleway:wght@300;400;600;700;800&display=swap');

/* ==================== LUXE NAVBAR ==================== */
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


/* Elegant pattern overlay */
.header::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        repeating-linear-gradient(
            45deg,
            transparent,
            transparent 20px,
            rgba(225, 145, 132, 0.03) 20px,
            rgba(225, 145, 132, 0.03) 40px
        );
    pointer-events: none;
}

/* Shine effect */
.header::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shine 8s infinite;
}

.header-center {
    display: none;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* ===== VOICE BUTTON (circle, green border, only icon) ===== */
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
}

.voice-commander-btn i {
    font-size: 20px;
    color: var(--green);
    transition: all 0.3s;
}

.voice-commander-btn:hover {
    transform: translateY(-2px);
    border-color: var(--green);
    box-shadow: 0 8px 20px rgba(71, 91, 53, 0.4);
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    border: 3px solid var(--vampire);
    overflow:hidden;
}

.notifications:hover {
    transform: translateY(-2px) rotate(8deg);
    border-color: var(--vampire);
    box-shadow: 0 8px 20px rgba(98, 6, 7, 0.35);
}

.notifications i {
    font-size: 20px;
    color: var(--green);
    transition: all 0.3s;
    display:inline-block;
    transform: rotate(0deg) !important;
}

.notifications:hover i {
    color: var(--vampire);
    transform: rotate(0deg) !important;
    animation:none;
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
    box-shadow: 0 2px 8px rgba(98, 6, 7, 0.3);
    font-family: 'Raleway', sans-serif;
    animation: badgePulse 2s ease-in-out infinite;
}

/* ===== PROFILE DROPDOWN (berry border) ===== */
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
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    border: 3px solid var(--berry);
}

.profile-dropdown:hover {
    transform: translateY(-2px);
    border-color: var(--berry);
    box-shadow: 0 10px 24px rgba(198, 62, 78, 0.35);
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

.profile-dropdown.open i{
    transform:rotate(180deg);
    color: var(--berry);
}
.profile-dropdown:hover i {
    color: var(--berry);

}

/* ===== DROPDOWN MENU BORDER (berry) ===== */
.dropdown-menu {
    position: absolute;
    top: 62px;
    right: 0;
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
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
        margin-left: 280px;
        margin-top: 75px;
        min-height: calc(100vh - 70px);
    }

    /* ==================== MODALS (unchanged – full HTML) ==================== */

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

    <div class="main-content">
        @include('partials.client.header')
        @yield('content')
    </div>

    <header class="header">
        <div class="header-left">

             <h1 style="font-family: 'Comic Sans MS', 'Raleway', sans-serif; font-size: 33px; font-weight: 900; margin: 0; display: flex; align-items: center; gap: 2px; cursor: pointer; transition: all 0.3s;">
                <span style="color: #E19184;">E</span>
                <span style="color: #C63E4E;">v</span>
                <span style="color: #475B35;">e</span>
                <span style="color: #620607;">n</span>
                <span style="color: #E19184;">t</span>
                <span style="color: #C63E4E;">F</span>
                <span style="color: #475B35;">l</span>
                <span style="color: #620607;">o</span>
                <span style="color: #E19184;">w</span>
            </h1>

        </div>

        <div class="header-right">
            <aside class="client-sidebar">
    <a href="{{ route('client.dashboard') }}"
       class="sidebar-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="{{ route('client.events.index') }}"
       class="sidebar-link {{ request()->is('client/events*') ? 'active' : '' }}">
        <i class="fas fa-calendar-alt"></i> My Events
    </a>
    <a href="{{ route('client.messages') }}"
       class="sidebar-link {{ request()->routeIs('client.messages*') ? 'active' : '' }}">
        <i class="fas fa-comments"></i> Messages
    </a>
    <a href="{{ route('client.profile') }}"
       class="sidebar-link {{ request()->routeIs('client.profile*') ? 'active' : '' }}">
        <i class="fas fa-user"></i> Profile
    </a>
</aside>

            <!-- Voice Commander Button -->
             <button class="voice-commander-btn" id="voiceCommanderBtn">
                    <i class="fas fa-microphone"></i>
                </button>

            <!-- Notifications Bell -->
            <div class="notifications" id="notificationBellBtn">
                <i class="fas fa-bell"></i>
                <span class="badge" id="headerNotifBadge">0</span>
            </div>

            <!-- Profile Dropdown (click toggle) -->
            <div class="profile-dropdown" id="profileDropdownBtn">
                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" alt="Profile">
                <span>{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down"></i>
                <div class="dropdown-menu" id="profileDropdownMenu">
                    <a href="{{ route('client.profile') }}"><i class="fas fa-user"></i> Profile</a>
                    <a href="{{ route('client.settings') }}"><i class="fas fa-cog"></i> Settings</a>
                    <hr>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <script src="{{ asset('js/client-dashboard.js') }}"></script>
    <script src="{{ asset('js/client-notification.js') }}"></script>
    @stack('scripts')


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
                <button class="notif-tab" data-filter="event"><i class="fas fa-calendar"></i> Events</button>
                <button class="notif-tab" data-filter="message"><i class="fas fa-envelope"></i> Messages</button>
            </div>
            <div class="notif-list" id="notifModalList"></div>
            <div class="notif-modal-actions">
                <button class="notif-action-btn primary" id="modalMarkAllRead"><i class="fas fa-check-double"></i> Mark All Read</button>
                <button class="notif-action-btn secondary" id="modalClearAll"><i class="fas fa-trash-alt"></i> Clear All</button>
            </div>
        </div>
    </div>

    <!-- ==================== FULL VOICE COMMANDER MODAL (restored) ==================== -->
    <div class="voice-commander-modal" id="voiceCommanderModal">
        <div class="voice-modal-overlay"></div>
        <div class="voice-modal-content">
            <button class="voice-close-btn" id="voiceCloseBtn"><i class="fas fa-times"></i></button>
            <div class="voice-header">
                <div class="voice-icon-pulse" id="voiceIconPulse">
                    <div class="pulse-ring"></div>
                    <div class="pulse-ring"></div>
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
                    <span class="chip">"Show guests"</span>
                </div>
            </div>
            <div class="voice-transcript" id="voiceTranscript"></div>
            <button class="btn-voice-toggle" id="voiceToggleBtn">
                <i class="fas fa-microphone"></i> Start Listening
            </button>
        </div>
    </div>

    <!-- Mood modal (optional – kept but button removed) -->
    <div class="mood-modal" id="moodModal">...</div>

    <script src="{{ asset('js/mood-voice-common.js') }}"></script>

    <!-- Click-toggle for profile dropdown -->
    <script>
     document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('profileDropdownBtn');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    if (dropdownBtn && dropdownMenu) {
        const chevron = dropdownBtn.querySelector('i'); // the chevron icon
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdownMenu.classList.toggle('show');
            // Rotate chevron based on open/close state
            if (isOpen) {
                chevron.style.transform = 'rotate(180deg)';
            } else {
                chevron.style.transform = 'rotate(0deg)';
            }
        });
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target)) {
                dropdownMenu.classList.remove('show');
                chevron.style.transform = 'rotate(0deg)';
            }
        });
    }
});
    </script>
</body>
</html>
