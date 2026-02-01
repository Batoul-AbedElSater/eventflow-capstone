<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - EventFlow</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
    
    @stack('styles') {{-- Additional page-specific styles --}}
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <h1 class="logo">EventFlow</h1>
        </div>
        <div class="header-center">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search events...">
            </div>
        </div>
        <div class="header-right">
            <!-- Notifications -->
            <div class="notifications">
                <i class="fas fa-bell"></i>
                <span class="badge">3</span>
            </div>
            
            <!-- Profile Dropdown -->
            <div class="profile-dropdown">
                <img src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" alt="Profile">
                <span>{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down"></i>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-menu">
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

    <!-- Sidebar -->
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <a href="{{ route('client.dashboard') }}" class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('client.events.index') }}" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>My Events</span>
            </a>
            <a href="{{ route('client.messages') }}" class="nav-item">
                <i class="fas fa-comments"></i>
                <span>Messages</span>
                <span class="badge">2</span>
            </a>
            <a href="{{ route('client.profile') }}" class="nav-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="{{ route('client.settings') }}" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- JavaScript -->
    <script src="{{ asset('js/client-dashboard.js') }}"></script>
    @stack('scripts') {{-- Additional page-specific scripts --}}
</body>
</html>