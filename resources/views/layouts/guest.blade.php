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

    <style>
        :root {
            --bg-primary: #090c15;
            --bg-card: #111827;
            --border-color: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --primary-accent: #6366f1;
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: var(--font-family);
            min-height: 100vh;
        }

        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .auth-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .form-control {
            background-color: #1f2937;
            border: 1px solid #374151;
            color: #fff;
            border-radius: 8px;
            padding: 12px;
        }

        .form-control:focus {
            background-color: #1f2937;
            border-color: var(--primary-accent);
            color: #fff;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.4);
            opacity: 0.95;
        }

        .text-link {
            color: var(--primary-accent);
            text-decoration: none;
            font-weight: 500;
        }

        .text-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Brand Logo -->
            <div class="text-center mb-4">
                <a href="/" class="d-inline-flex align-items-center text-decoration-none text-light fw-bold fs-3 text-uppercase">
                    <i class="bi bi-cpu text-info me-2 fs-2"></i>
                    AI <span class="text-info ms-1">Prep</span>
                </a>
            </div>
            
            {{ $slot }}
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
