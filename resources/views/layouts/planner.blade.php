<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - EventFlow Planner</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/planner-dashboard.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <h1 class="logo">EventFlow<span>Pro</span></h1>
        </div>
        <div class="header-center">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search events, clients, tasks...">
            </div>
        </div>
        <div class="header-right">
            <!-- Quick Actions -->
            <div class="quick-action-btn" title="Add Event">
                <i class="fas fa-plus-circle"></i>
            </div>
            
            <!-- Notifications -->
            <div class="notifications">
                <i class="fas fa-bell"></i>
                <span class="badge">5</span>
            </div>
            
            <!-- Profile Dropdown -->
            <div class="profile-dropdown">
                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=C63E4E&color=F5F9E5' }}" alt="Profile">
                <span>{{ Auth::user()->name }}</span>
                <i class="fas fa-chevron-down"></i>
                
                <!-- Dropdown Menu -->
                <div class="dropdown-menu">
                    <div class="dropdown-header">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=C63E4E&color=F5F9E5' }}" alt="Profile">
                        <div>
                            <strong>{{ Auth::user()->name }}</strong>
                            <span>{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                    <hr>
                    <a href="#"><i class="fas fa-user"></i> My Profile</a>
                    <a href="#"><i class="fas fa-cog"></i> Settings</a>
                    <a href="#"><i class="fas fa-chart-line"></i> Analytics</a>
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

    <!-- Sidebar -->
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <a href="{{ route('planner.dashboard') }}" class="nav-item active">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-inbox"></i>
                <span>Event Requests</span>
                <span class="badge">3</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>My Events</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-tasks"></i>
                <span>Tasks</span>
                <span class="badge">12</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-envelope"></i>
                <span>Messages</span>
                <span class="badge">8</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
        </nav>
        
        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="quick-stats">
                <div class="quick-stat">
                    <i class="fas fa-calendar-check"></i>
                    <div>
                        <strong>8</strong>
                        <span>Active Events</span>
                    </div>
                </div>
                <div class="quick-stat">
                    <i class="fas fa-dollar-sign"></i>
                    <div>
                        <strong>$45K</strong>
                        <span>This Month</span>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- JavaScript -->
    <script src="{{ asset('js/planner-dashboard.js') }}"></script>
    @stack('scripts')
</body>
</html>