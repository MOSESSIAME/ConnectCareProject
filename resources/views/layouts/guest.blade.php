<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ConnectCare CMS') }}</title>

    <!-- ✅ CSRF Protection Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- ✅ Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            color: #333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary {
            background: #0d6efd;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
        }

        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- ✅ Main Content -->
    <div class="w-100">
        @yield('content')
    </div>

    {{-- <footer>
        &copy; {{ date('Y') }} ConnectCare CMS. All rights reserved.
    </footer> --}}

    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ CSRF Setup for AJAX (important for login/session safety) -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            window.axios && (window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token);
        });
    </script>
</body>
</html>
