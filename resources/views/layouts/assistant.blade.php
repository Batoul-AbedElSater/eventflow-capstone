<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Assistant Dashboard') - EventFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; position: fixed; height: 100vh; overflow-y: auto; box-shadow: 2px 0 15px rgba(0,0,0,0.1); }
        .sidebar .logo { font-size: 24px; font-weight: bold; margin-bottom: 30px; display: flex; align-items: center; gap: 10px; }
        .sidebar nav ul { list-style: none; }
        .sidebar nav ul li { margin-bottom: 15px; }
        .sidebar nav ul li a { color: rgba(255,255,255,0.9); text-decoration: none; display: flex; align-items: center; gap: 12px; padding: 10px 15px; border-radius: 8px; transition: all 0.3s; }
        .sidebar nav ul li a:hover, .sidebar nav ul li a.active { background: rgba(255,255,255,0.2); color: white; }
        .main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }
        .header { background: white; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; }
        .header h1 { color: #333; font-size: 24px; }
        .header-right { display: flex; align-items: center; gap: 20px; }
        .user-menu { display: flex; align-items: center; gap: 10px; }
        .user-menu a { color: #667eea; text-decoration: none; transition: color 0.3s; }
        .user-menu a:hover { color: #764ba2; }
        .content { flex: 1; padding: 30px; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; width: 100%; z-index: 1000; }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .toggle-sidebar { display: block; }
        }
        .toggle-sidebar { display: none; background: none; border: none; color: #667eea; font-size: 24px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <i class="fas fa-person-hiking"></i>
                <span>EventFlow</span>
            </div>
            <nav>
                <ul>
                    <li>
                        <a href="{{ route('assistant.dashboard') }}" class="{{ request()->routeIs('assistant.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="main-content">
            <header class="header">
                <h1>@yield('page-title', 'Dashboard')</h1>
                <div class="header-right">
                    <div class="user-menu">
                        <span>{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #667eea; cursor: pointer; text-decoration: underline;">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="content">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
