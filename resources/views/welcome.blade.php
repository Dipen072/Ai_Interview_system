<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AI Interview Preparation System</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #070a13;
            --bg-secondary: #0f172a;
            --bg-card: #1e293b;
            --border-color: #334155;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --primary-accent: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --secondary-gradient: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
            --font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-family);
            overflow-x: hidden;
        }

        .hero-section {
            background: radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.15) 0%, rgba(7, 10, 19, 0) 60%);
            padding: 100px 0 80px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            padding: 12px 28px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
            opacity: 0.95;
        }

        .btn-secondary {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 8px;
            padding: 12px 28px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: var(--border-color);
            color: #fff;
            transform: translateY(-2px);
        }

        .card {
            background-color: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-accent);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .text-indigo {
            color: #818cf8 !important;
        }

        .feature-icon {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-accent);
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

    <!-- Simple Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center fw-bold text-uppercase" href="/">
                <i class="bi bi-cpu text-info me-2 fs-3"></i>
                <span>AI Interview <span class="text-info">Prep</span></span>
            </a>
            
            <div class="ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm px-4">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary btn-sm px-4 me-2">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <span class="badge bg-indigo-subtle text-info border border-info mb-3 px-3 py-2 text-uppercase font-monospace" style="background: rgba(6, 182, 212, 0.1); font-size: 0.8rem;">Powered by Gemini & OpenAI</span>
                    <h1 class="display-3 fw-bold text-white mb-3">Master Your Next Technical Interview</h1>
                    <p class="lead text-secondary mb-4 fs-5" style="line-height: 1.6;">
                        Generate dynamically tailored mock exams based on your tech stack. Receive immediate, granular AI evaluation sheets comparing your answers side-by-side with model concepts.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5">Go to Workspace</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">Get Started Free</a>
                            <a href="{{ route('login') }}" class="btn btn-secondary btn-lg px-5">Sign In</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" style="background-color: var(--bg-secondary);">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-white mb-2">Features Built For Career Acceleration</h2>
                <p class="text-secondary">Enterprise-grade modules designed to help candidates perform better under pressure.</p>
            </div>

            <div class="row g-4">
                <!-- Feature 1 -->
                <div class="col-md-4">
                    <div class="card p-4 h-100">
                        <div class="feature-icon mb-3">
                            <i class="bi bi-robot fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2">Adaptive AI Questions</h4>
                        <p class="text-secondary m-0" style="font-size: 0.95rem; line-height: 1.5;">
                            Dynamic generation customized to match PHP, Laravel, MySQL, JavaScript, or custom administrative categories at Easy, Medium, or Hard difficulty.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="col-md-4">
                    <div class="card p-4 h-100">
                        <div class="feature-icon mb-3" style="color: #06b6d4; background: rgba(6, 182, 212, 0.1);">
                            <i class="bi bi-clock-history fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2">Distraction-Free Arena</h4>
                        <p class="text-secondary m-0" style="font-size: 0.95rem; line-height: 1.5;">
                            Attempt questions one-by-one with real-time AJAX auto-saving, visual indicators, side-navigation grids, and automated submission triggers.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div class="col-md-4">
                    <div class="card p-4 h-100">
                        <div class="feature-icon mb-3" style="color: #a855f7; background: rgba(168, 85, 247, 0.1);">
                            <i class="bi bi-bar-chart-steps fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2">Detailed Analytics Sheets</h4>
                        <p class="text-secondary m-0" style="font-size: 0.95rem; line-height: 1.5;">
                            Instant score metrics (out of 10) evaluating Accuracy, Technical Depth, Communication, and Completeness, with actionable improvement guidelines.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 text-center border-top border-secondary" style="font-size: 0.85rem; color: var(--text-secondary);">
        <div class="container">
            <p class="m-0">&copy; {{ date('Y') }} AI Interview Preparation System. Developed with Laravel 12 & Gemini AI.</p>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
