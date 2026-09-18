<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JARA - Manage Tasks. Collaborate Better. Finish Faster.</title>

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
            --jara-purple: #7c3aed;
            --jara-dark: #0f172a;
            --jara-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--jara-bg);
            color: #0f172a;
            overflow-x: hidden;
        }

        .navbar-brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .hero-section {
            padding: 6rem 0 5rem;
            background: radial-gradient(circle at 50% 0%, #ede9fe 0%, #f8fafc 70%);
        }

        .btn-jara-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.85rem 1.75rem;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
            transition: all 0.2s ease;
        }

        .btn-jara-primary:hover {
            background: linear-gradient(135deg, #4338ca, #4f46e5);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
        }

        .feature-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            background: #ffffff;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .feature-icon-wrapper {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .mockup-window {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.12);
            overflow: hidden;
        }

        .mockup-header {
            background: #0f172a;
            padding: 0.85rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

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
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark fs-4" href="{{ route('home') }}">
                <span class="navbar-brand-icon">
                    <i class="bi bi-kanban-fill"></i>
                </span>
                <span>JARA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item"><a class="nav-link fw-semibold text-secondary active" href="{{ route('home') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-secondary" href="#features">Fitur Utama</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold text-secondary" href="#demo">Preview</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('tasks.index') }}" class="btn btn-jara-primary d-inline-flex align-items-center gap-2">
                            <i class="bi bi-speedometer2"></i>
                            <span>Buka Dashboard</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-light border fw-semibold px-3">Login</a>
                        <a href="{{ route('register') }}" class="btn btn-jara-primary">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <span class="badge bg-indigo-subtle text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-semibold mb-3">
                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i> The Next-Gen Productivity Workspace
                    </span>
                    <h1 class="display-3 fw-extrabold text-dark tracking-tight mb-3">
                        Manage Tasks.<br>
                        Collaborate Better.<br>
                        <span style="background: linear-gradient(135deg, #4f46e5, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Finish Faster.</span>
                    </h1>
                    <p class="lead text-muted mb-4 mx-auto" style="max-width: 680px;">
                        JARA adalah aplikasi manajemen tugas canggih untuk individu dan tim. Atur prioritas, delegasikan pekerjaan, pantau tenggat waktu, dan selesaikan proyek lebih terstruktur.
                    </p>
                    <div class="d-flex justify-content-center gap-3 mb-5">
                        @auth
                            <a href="{{ route('tasks.index') }}" class="btn btn-jara-primary btn-lg px-4">
                                <i class="bi bi-arrow-right-circle me-2"></i> Masuk ke Dashboard Saya
                            </a>
                            <a href="{{ route('task-lists.index') }}" class="btn btn-outline-dark btn-lg px-4">
                                <i class="bi bi-folder2 me-2"></i> Daftar Task List
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-jara-primary btn-lg px-4">
                                <i class="bi bi-rocket-takeoff-fill me-2"></i> Mulai Sekarang Gratis
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-dark btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk Akun
                            </a>
                        @endauth
                    </div>

                    <!-- SaaS Mockup Window -->
                    <div class="mockup-window text-start mx-auto" id="demo" style="max-width: 920px;">
                        <div class="mockup-header justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="dot bg-danger"></span>
                                <span class="dot bg-warning"></span>
                                <span class="dot bg-success"></span>
                                <span class="text-white-50 small ms-2 font-monospace">jara.workspace/tasks</span>
                            </div>
                            <span class="badge bg-secondary-subtle text-white-50 small font-monospace">Live Preview</span>
                        </div>
                        <div class="p-4 bg-white">
                            <!-- Metrics cards in mockup -->
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small fw-bold">TOTAL TASKS</div>
                                        <div class="fs-4 fw-bold text-dark">12</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small fw-bold">COMPLETED</div>
                                        <div class="fs-4 fw-bold text-success">8</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small fw-bold">IN PROGRESS</div>
                                        <div class="fs-4 fw-bold text-primary">3</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="text-muted small fw-bold">OVERDUE</div>
                                        <div class="fs-4 fw-bold text-danger">1</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sample Task Rows -->
                            <div class="d-flex flex-column gap-2">
                                <div class="border rounded-3 p-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Design UI/UX Mockups with Figma</div>
                                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> Due in 2 days &bull; General Tasks</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge badge-priority-High px-3 py-1">High</span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">Completed</span>
                                    </div>
                                </div>

                                <div class="border rounded-3 p-3 bg-white d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-circle text-muted fs-4"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Implement Authentication & RBAC</div>
                                            <small class="text-muted"><i class="bi bi-calendar3 me-1"></i> Due Tomorrow &bull; Backend Sprint</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge badge-priority-Medium px-3 py-1">Medium</span>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-3 py-1">In Progress</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3 Core Feature Sections (Task Management, Team Collaboration, Progress Tracking) -->
    <section id="features" class="py-5 bg-white border-top border-bottom">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-3 py-2 rounded-pill">Features</span>
                <h2 class="display-6 fw-bold text-dark mt-2">Dibuat untuk Produktivitas Maksimal</h2>
                <p class="text-muted mx-auto" style="max-width: 620px;">
                    Tiga pilar utama JARA yang membantu Anda dan tim bekerja lebih efisien setiap hari.
                </p>
            </div>

            <div class="row g-4">
                <!-- 1. Task Management -->
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon-wrapper bg-primary-subtle text-primary">
                            <i class="bi bi-check2-square"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">1. Task Management</h4>
                        <p class="text-muted mb-3">
                            Kelola tugas dengan sistem prioritas (High, Medium, Low), deadline dinamis, deskripsi mendalam, serta update status cepat secara visual.
                        </p>
                        <ul class="list-unstyled text-secondary small mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check2 text-primary me-2"></i>Filter status & prioritas interaktif</li>
                            <li><i class="bi bi-check2 text-primary me-2"></i>Tenggat waktu & peringatan overdue</li>
                            <li><i class="bi bi-check2 text-primary me-2"></i>Organisasi multi-task list</li>
                        </ul>
                    </div>
                </div>

                <!-- 2. Team Collaboration -->
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon-wrapper bg-purple-subtle text-purple" style="background-color: #f3e8ff; color: #7c3aed;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">2. Team Collaboration</h4>
                        <p class="text-muted mb-3">
                            Undang anggota tim ke dalam daftar tugas atau proyek Anda. Setiap anggota dapat melihat, memperbarui status, dan berkolaborasi dalam satu wadah.
                        </p>
                        <ul class="list-unstyled text-secondary small mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check2 text-primary me-2"></i>Hak akses pemilik & kolaborator</li>
                            <li><i class="bi bi-check2 text-primary me-2"></i>Delegasi tugas kepada anggota</li>
                            <li><i class="bi bi-check2 text-primary me-2"></i>Manajemen anggota mudah</li>
                        </ul>
                    </div>
                </div>

                <!-- 3. Progress Tracking -->
                <div class="col-md-4">
                    <div class="feature-card p-4 h-100">
                        <div class="feature-icon-wrapper bg-success-subtle text-success">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">3. Progress Tracking</h4>
                        <p class="text-muted mb-3">
                            Pantau persentase penyelesaian setiap daftar tugas secara langsung melalui progress bar otomatis dan kartu ringkasan status pekerjaan.
                        </p>
                        <ul class="list-unstyled text-secondary small mb-0 d-flex flex-column gap-2">
                            <li><i class="bi bi-check2 text-primary me-2"></i>Metrik persentase tugas selesai</li>
                            <li><i class="bi bi-check2 text-primary me-2"></i>Ringkasan tugas selesai vs pending</li>
                            <li><i class="bi bi-check2 text-primary me-2"></i>Visibilitas target harian</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 bg-white text-center text-muted small">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="navbar-brand-icon" style="width: 28px; height: 28px; font-size: 0.9rem;">
                    <i class="bi bi-kanban-fill"></i>
                </span>
                <span class="fw-bold text-dark fs-6">JARA</span>
            </div>
            <p class="mb-1">&copy; 2026 <strong>JARA</strong> - Job Activity & Responsibility Assistant.</p>
            <p class="mb-0 text-secondary">A Modern Productivity Web Application built with Laravel 11 & Bootstrap 5.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
