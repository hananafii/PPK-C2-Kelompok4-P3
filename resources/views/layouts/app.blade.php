<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'JARA') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --jara-primary: #4f46e5;
            --jara-primary-hover: #4338ca;
            --jara-primary-light: #eef2ff;
            --jara-purple: #7c3aed;
            --jara-sidebar-bg: #0f172a;
            --jara-sidebar-border: #1e293b;
            --jara-sidebar-hover: #1e293b;
            --jara-sidebar-active: #4f46e5;
            --jara-bg: #f8fafc;
            --jara-card-border: #e2e8f0;
            --jara-text-main: #0f172a;
            --jara-text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--jara-bg);
            color: var(--jara-text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Layout Structure */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Sidebar Styling (Desktop Fixed) */
        .sidebar {
            width: 260px;
            background-color: var(--jara-sidebar-bg);
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--jara-sidebar-border);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1020;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--jara-sidebar-border);
        }

        .brand-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.15rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-heading {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 0.75rem 0.75rem 0.35rem;
            font-weight: 700;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            margin-bottom: 0.2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.15s ease-in-out;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            transition: transform 0.15s ease;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: var(--jara-sidebar-hover);
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.1);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--jara-sidebar-active);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--jara-sidebar-border);
            background-color: rgba(0, 0, 0, 0.15);
        }

        /* Main Content Container */
        .main-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--jara-card-border);
            padding: 0.85rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 1010;
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.95);
        }

        .content-container {
            padding: 2rem 2.25rem;
            flex-grow: 1;
        }

        /* SaaS Card Aesthetics */
        .card {
            background: #ffffff;
            border: 1px solid var(--jara-card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -6px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        /* Modern Badges (High, Medium, Low & Statuses) */
        .badge-priority-High, .badge-danger-soft {
            background-color: #fef2f2;
            color: #dc2626;
            border: 1px solid #fee2e2;
            font-weight: 600;
        }
        .badge-priority-Medium, .badge-warning-soft {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
            font-weight: 600;
        }
        .badge-priority-Low, .badge-success-soft {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
            font-weight: 600;
        }

        .badge-status-Pending {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .badge-status-InProgress, .badge-status-In-Progress {
            background-color: #f0f9ff;
            color: #0284c7;
            border: 1px solid #e0f2fe;
            font-weight: 600;
        }
        .badge-status-Completed {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #dcfce7;
            font-weight: 600;
        }

        /* Buttons */
        .btn-jara-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
            transition: all 0.2s ease;
        }
        .btn-jara-primary:hover {
            background: linear-gradient(135deg, #4338ca, #4f46e5);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        /* Avatar styling */
        .avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            color: #4338ca;
            font-weight: 700;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Empty State Graphic Container */
        .empty-state-box {
            padding: 3.5rem 2rem;
            text-align: center;
            background: #ffffff;
            border: 2px dashed #e2e8f0;
            border-radius: 16px;
        }
        .empty-state-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        /* Footer */
        .app-footer {
            background: #ffffff;
            border-top: 1px solid var(--jara-card-border);
            padding: 1.25rem 2.25rem;
            color: var(--jara-text-muted);
            font-size: 0.82rem;
        }

        /* Responsive Mobile Drawer */
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: -260px;
            }
            .sidebar.show {
                left: 0;
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(2px);
                z-index: 1015;
            }
            .sidebar-backdrop.show {
                display: block;
            }
            .content-container {
                padding: 1.5rem 1rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-wrapper">
        <!-- Mobile Sidebar Backdrop -->
        <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="appSidebar">
            <div class="sidebar-brand">
                <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 text-decoration-none text-white">
                    <div class="brand-logo-icon">
                        <i class="bi bi-kanban-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 tracking-tight lh-1">JARA</div>
                        <div style="font-size: 0.65rem; color: #94a3b8;" class="text-uppercase tracking-wider">Productivity SaaS</div>
                    </div>
                </a>
            </div>

            <div class="sidebar-menu">
                <div class="sidebar-heading">Workspace</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('task-lists.*') ? 'active' : '' }}" href="{{ route('task-lists.index') }}">
                            <i class="bi bi-folder2-open"></i>
                            <span>Task Lists</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                            <i class="bi bi-check2-square"></i>
                            <span>Task Management</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('lists.*') || request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('lists.index') }}">
                            <i class="bi bi-people-fill"></i>
                            <span>Collaboration</span>
                        </a>
                    </li>
                </ul>

                @if(Auth::check() && (Auth::user()->is_admin || (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin())))
                    <div class="sidebar-heading mt-3">Administration</div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users*') || request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                <i class="bi bi-shield-lock-fill"></i>
                                <span>User Management</span>
                            </a>
                        </li>
                    </ul>
                @endif

                <div class="sidebar-heading mt-3">Quick Navigation</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Public Landing</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Sidebar User Profile Info -->
            @auth
            <div class="sidebar-footer">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-circle">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="overflow-hidden flex-grow-1">
                        <div class="text-white text-truncate fw-semibold small">{{ Auth::user()->name }}</div>
                        <div class="small text-truncate" style="color: #64748b; font-size: 0.72rem;">{{ Auth::user()->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" id="sidebarLogoutForm">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-link text-secondary p-0 text-decoration-none" title="Logout">
                            <i class="bi bi-box-arrow-right fs-5"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Top Navbar -->
            <header class="top-navbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none border p-1 px-2" type="button" onclick="toggleSidebar()">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="mb-0 fw-bold text-dark">@yield('page-title', 'Workspace')</h5>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-jara-primary d-none d-sm-inline-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg"></i>
                        <span>New Task</span>
                    </a>

                    @auth
                    <!-- User Dropdown Menu -->
                    <div class="dropdown">
                        <button class="btn btn-light border rounded-pill py-1 px-2 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="small fw-semibold text-dark d-none d-md-inline">{{ Auth::user()->name }}</span>
                            <i class="bi bi-chevron-down text-muted small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2" style="min-width: 220px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold text-dark text-truncate">{{ Auth::user()->name }}</div>
                                <div class="text-muted small text-truncate">{{ Auth::user()->email }}</div>
                                <div class="mt-1">
                                    <span class="badge {{ Auth::user()->is_admin ? 'bg-danger' : 'bg-primary' }} rounded-pill" style="font-size: 0.65rem;">
                                        {{ Auth::user()->is_admin ? 'ADMIN' : 'MEMBER' }}
                                    </span>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('task-lists.index') }}">
                                    <i class="bi bi-folder2 me-2 text-muted"></i> My Task Lists
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="{{ route('tasks.index') }}">
                                    <i class="bi bi-check2-square me-2 text-muted"></i> My Tasks
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-light border">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-jara-primary">Register</a>
                    </div>
                    @endauth
                </div>
            </header>

            <!-- Main Workspace Content -->
            <main class="content-container">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div class="fw-medium">{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                        <div class="fw-medium">{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <span>Please review the errors below:</span>
                        </div>
                        <ul class="mb-0 ps-4 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- App Footer -->
            <footer class="app-footer mt-auto d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                <div>
                    &copy; 2026 <strong>JARA</strong> &bull; Job Activity & Responsibility Assistant.
                </div>
                <div class="d-flex align-items-center gap-3 text-muted">
                    <span>Release V2.0</span>
                    <span>&bull;</span>
                    <span>Productivity SaaS</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vanilla JS Helper for Responsive Sidebar -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('appSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
