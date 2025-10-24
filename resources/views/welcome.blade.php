<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ConnectCare CMS') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Animate.css (optional for fade effects) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">

    <!-- ✅ CSRF Token (needed for any JS forms or AJAX) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        .welcome-container {
            height: 100vh;
        }
        .card-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-radius: 20px;
        }
        .btn-primary {
            border-radius: 30px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="welcome-container d-flex flex-column justify-content-center align-items-center text-center px-3">
        <div class="card-glass p-5 shadow-lg animate__animated animate__fadeInUp">
            <h1 class="fw-bold text-primary mb-2 display-5">ConnectCare</h1>
            <h5 class="text-secondary mb-4">Church Management System</h5>

            <p class="lead text-muted mb-5 px-3">
                Manage <strong>members</strong>, <strong>follow-ups</strong>, and <strong>reports</strong> —<br>
                Empowering your ministry with excellence and simplicity.
            </p>

            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-2"></i> Login
            </a>
        </div>

        <footer class="mt-5 text-muted small">
            &copy; {{ date('Y') }} ConnectCare CMS. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ CSRF Setup for AJAX (future-proofing your forms) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            window.Laravel = { csrfToken: token };
        });
    </script>
</body>
</html>
