<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Interview System') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Custom CSS (Dark Theme System) -->
    <style>
        :root {
            --bg-primary: #0b0f19;
            --bg-secondary: #111827;
            --bg-card: #1f2937;
            --border-color: #374151;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --primary-accent: #6366f1; /* Indigo */
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --secondary-gradient: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-family);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-header {
            background-color: rgba(0,0,0,0.15);
            border-bottom: 1px solid var(--border-color);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.4);
            opacity: 0.95;
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: var(--border-color);
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-info {
            background: var(--secondary-gradient);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 10px 20px;
            box-shadow: 0 4px 10px rgba(6, 182, 212, 0.3);
        }

        .btn-info:hover {
            color: white;
            transform: translateY(-2px);
            opacity: 0.95;
        }

        .navbar {
            background-color: var(--bg-secondary) !important;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar {
            background-color: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            min-height: calc(100vh - 56px);
        }

        .sidebar .nav-link {
            color: var(--text-secondary);
            border-radius: 8px;
            margin: 4px 12px;
            padding: 10px 15px;
            font-weight: 500;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: var(--bg-card);
            color: var(--primary-accent);
        }

        .text-muted-custom {
            color: var(--text-secondary) !important;
        }

        /* Glassmorphism utility */
        .glass-panel {
            background: rgba(31, 41, 55, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-primary);
        }
        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
</head>
<body class="d-flex flex-column h-100">

    <!-- Top Navbar -->
    @include('layouts.navigation')

    <div class="container-fluid flex-grow-1">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('interviews.setup') ? 'active' : '' }}" href="{{ route('interviews.setup') }}">
                                <i class="bi bi-play-circle me-2"></i> Start Interview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-circle me-2"></i> My Profile
                            </a>
                        </li>
                        
                        @role('Admin')
                        <hr class="mx-3 my-2 border-secondary">
                        <div class="px-3 py-1 text-uppercase text-secondary font-monospace" style="font-size: 0.75rem;">Admin Panel</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-shield-lock me-2"></i> Admin Console
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-tags me-2"></i> Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people me-2"></i> Users & Roles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                                <i class="bi bi-gear me-2"></i> AI Configuration
                            </a>
                        </li>
                        @endrole
                    </ul>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 flex-grow-1">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 bg-success-subtle text-success-emphasis" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger-subtle text-danger-emphasis" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show border-0 bg-info-subtle text-info-emphasis" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
