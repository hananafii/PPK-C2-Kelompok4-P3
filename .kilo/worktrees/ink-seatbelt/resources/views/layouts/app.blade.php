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
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --jara-primary: #4f46e5;
            --jara-primary-hover: #4338ca;
            --jara-primary-light: #eef2ff;
            --jara-sidebar-bg: #1e1b4b;
            --jara-sidebar-hover: #312e81;
            --jara-sidebar-active: #4338ca;
            --jara-bg: #f8fafc;
            --jara-card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--jara-bg);
            color: #1e293b;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: var(--jara-sidebar-bg);
            min-height: 100vh;
            color: #cbd5e1;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .sidebar .brand {
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }

        .sidebar .nav-link {
            color: #94a3b8;
            font-weight: 500;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin: 0.25rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background-color: var(--jara-sidebar-hover);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--jara-sidebar-active);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .sidebar-heading {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            padding: 1rem 1.25rem 0.5rem;
            font-weight: 700;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--jara-card-border);
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Card Enhancements */
        .card {
            border: 1px solid var(--jara-card-border);
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        }

        /* Status & Priority Badges */
        .badge-priority-High {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .badge-priority-Medium {
            background-color: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .badge-priority-Low {
            background-color: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .badge-status-Pending {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .badge-status-InProgress, .badge-status-In-Progress {
            background-color: #e0f2fe;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }
        .badge-status-Completed {
            background-color: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .btn-jara {
            background-color: var(--jara-primary);
            color: #ffffff;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            border: none;
            transition: all 0.2s;
        }
        .btn-jara:hover {
            background-color: var(--jara-primary-hover);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .progress-bar-jara {
            background: linear-gradient(90deg, #4f46e5, #06b6d4);
        }

        /* Avatar styling */
        .avatar-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e0e7ff;
            color: #4338ca;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-group .avatar-circle {
            margin-right: -8px;
            border: 2px solid #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
                <div class="brand">
                    <div class="brand-icon">
                        <i class="bi bi-kanban-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white fs-5 lh-1">JARA</div>
                        <small class="text-white-50" style="font-size: 0.65rem;">Task & Responsibility</small>
                    </div>
                </div>

                <div class="py-3">
                    <div class="sidebar-heading">Workspace</div>
                    <ul class="nav flex-column">
                        @if(Route::has('dashboard'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        @endif
                        @if(Route::has('lists.index'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('lists.*') ? 'active' : '' }}" href="{{ route('lists.index') }}">
                                <i class="bi bi-folder2"></i>
                                <span>Task Lists</span>
                            </a>
                        </li>
                        @elseif(Route::has('task-lists.index'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('task-lists.*') ? 'active' : '' }}" href="{{ route('task-lists.index') }}">
                                <i class="bi bi-folder2"></i>
                                <span>Task Lists</span>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}">
                                <i class="bi bi-check2-square"></i>
                                <span>Task Management</span>
                            </a>
                        </li>
                        @if(Auth::user()?->isAdmin())
                            @if(Route::has('users.index'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <i class="bi bi-people-fill"></i>
                                    <span>User Management</span>
                                </a>
                            </li>
                            @elseif(Route::has('admin.users'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                    <i class="bi bi-people-fill"></i>
                                    <span>User Management</span>
                                </a>
                            </li>
                            @endif
                        @endif
                    </ul>

                    <div class="sidebar-heading mt-4">Account</div>
                    <ul class="nav flex-column mb-4">
                        <li class="nav-item">
                            <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}" id="logoutFormSidebar">
                                @csrf
                                <a class="nav-link text-danger-emphasis" href="#" onclick="event.preventDefault(); document.getElementById('logoutFormSidebar').submit();">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Logout</span>
                                </a>
                            </form>
                        </li>
                    </ul>

                    <!-- User mini-badge at sidebar footer -->
                    <div class="px-3 mt-auto">
                        <div class="p-2 rounded-3 bg-white bg-opacity-10 d-flex align-items-center gap-2">
                            <div class="avatar-circle bg-primary text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <div class="text-white text-truncate fw-semibold small">{{ Auth::user()->name }}</div>
                                <span class="badge bg-{{ Auth::user()->isAdmin() ? 'danger' : 'info' }} bg-opacity-75" style="font-size: 0.65rem;">
                                    {{ strtoupper(Auth::user()->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-0">
                <!-- Top Navbar -->
                <header class="top-navbar d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-secondary d-md-none p-1 px-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <i class="bi bi-list fs-5"></i>
                        </button>
                        <h5 class="mb-0 fw-bold text-dark">@yield('page-title', 'Dashboard')</h5>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-jara d-none d-sm-inline-flex align-items-center gap-1">
                            <i class="bi bi-plus-lg"></i>
                            <span>New Task</span>
                        </a>

                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill border d-flex align-items-center gap-2 py-1 px-2" type="button" data-bs-toggle="dropdown">
                                <div class="avatar-circle" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="small fw-semibold d-none d-md-inline">{{ Auth::user()->name }}</span>
                                <i class="bi bi-chevron-down small text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                                <li class="px-3 py-2 border-bottom">
                                    <p class="mb-0 fw-bold">{{ Auth::user()->name }}</p>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                    <div class="mt-1">
                                        <span class="badge bg-{{ Auth::user()->isAdmin() ? 'danger' : 'primary' }}">
                                            {{ ucfirst(Auth::user()->role) }}
                                        </span>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="#" onclick="event.preventDefault(); document.getElementById('navbarLogoutForm').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </a>
                                    <form id="navbarLogoutForm" action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>

                <!-- Page Body Content -->
                <main class="p-3 p-md-4">
                    <!-- Flash Notifications -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 d-flex align-items-center gap-2" role="alert">
                            <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
                            <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Please check the following errors:</div>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
