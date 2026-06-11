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
        display: flex; align-items: center; justify-content: center;
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
        <div class="notifications">
            <i class="fas fa-bell"></i>
            <span class="badge">0</span>
        </div>

        <div class="profile-dropdown" id="profileDropdownBtn">
            <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=C63E4E&color=F5F9E5' }}" alt="Profile">
            <span>{{ Auth::user()->name }}</span>
            <i class="fas fa-chevron-down"></i>
            <div class="dropdown-menu" id="profileDropdownMenu">
                <hr>
                <a href="#"><i class="fas fa-user"></i> My Profile</a>
                <a href="#"><i class="fas fa-cog"></i> Settings</a>
                <hr>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

<aside class="assistant-sidebar">
    <div class="sidebar-section-label">Main</div>



    <a href="{{ route('assistant.tasks') }}"
       class="sidebar-link {{ request()->routeIs('assistant.tasks*') ? 'active' : '' }}">
        <i class="fas fa-tasks"></i> My Tasks
        @if(isset($pendingTasksCount) && $pendingTasksCount > 0)
            <span class="sidebar-badge">{{ $pendingTasksCount }}</span>
        @endif
    </a>

    <a href="{{ route('assistant.orders') }}"
   class="sidebar-link {{ request()->routeIs('assistant.orders') ? 'active' : '' }}">
    <i class="fas fa-shopping-cart"></i> My Orders
</a>

    <div class="sidebar-section-label">Account</div>

    <a href="#" class="sidebar-link">
        <i class="fas fa-user"></i> Profile
    </a>
</aside>

<main class="main-content">
    @yield('content')
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('profileDropdownBtn');
    const menu = document.getElementById('profileDropdownMenu');
    if (btn && menu) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('show');
            btn.classList.toggle('open');
        });
        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target)) {
                menu.classList.remove('show');
                btn.classList.remove('open');
            }
        });
    }
});
</script>

@stack('scripts')
</body>
</html>