<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login JARA - Job Activity & Responsibility Assistant</title>

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
            --jara-bg: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #ede9fe 0%, #f8fafc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-card {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
            max-width: 440px;
            width: 100%;
            padding: 2.5rem;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            margin-bottom: 1.25rem;
        }

        .btn-jara-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            width: 100%;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: all 0.2s ease;
        }

        .btn-jara-primary:hover {
            background: linear-gradient(135deg, #4338ca, #4f46e5);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }

        .form-control {
            border-radius: 10px;
            padding: 0.7rem 1rem;
            border: 1px solid #cbd5e1;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .password-toggle-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-left: none;
            color: #64748b;
            cursor: pointer;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .password-toggle-btn:hover {
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="{{ route('home') }}" class="text-decoration-none">
                <div class="brand-icon">
                    <i class="bi bi-kanban-fill"></i>
                </div>
            </a>
            <h1 class="h3 fw-bold text-dark mb-1">Login JARA</h1>
            <p class="text-muted small">Masuk ke akun Anda untuk mengelola tugas dan tim.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 small py-2 mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary" for="email">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-envelope"></i></span>
                    <input id="email" name="email" type="email" class="form-control border-start-0" value="{{ old('email') }}" placeholder="nama@email.com" style="border-radius: 0 10px 10px 0;" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold text-secondary" for="password">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 10px 0 0 10px;"><i class="bi bi-lock"></i></span>
                    <input id="password" name="password" type="password" class="form-control border-start-0 border-end-0" placeholder="••••••••" required>
                    <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('password', this)" title="Lihat password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-jara-primary mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </button>
        </form>

        <div class="text-center small text-muted pt-3 border-top">
            <p class="mb-1">Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none">Register</a></p>
            <p class="mb-0"><a href="{{ route('home') }}" class="text-secondary text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Beranda</a></p>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Vanilla JS Password Visibility Toggle -->
    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
