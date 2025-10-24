@extends('layouts.guest')

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100 bg-gradient"
     style="background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);">
    
    <div class="card shadow-lg border-0 rounded-4" style="width: 420px; background: #fff;">
        <div class="card-body p-5">

            <!-- App Header -->
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary mb-1">ConnectCare CMS</h3>
                <p class="text-muted small">Church Management System</p>
            </div>

            <!-- Flash / Error Messages -->
            @if (session('status'))
                <div class="alert alert-success text-center small">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger small">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ✅ Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf {{-- CSRF protection is essential for Laravel --}}
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input id="email" type="email" 
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Enter your email">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input id="password" type="password" 
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           name="password" required placeholder="Enter your password">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                </button>
            </form>

            <!-- Footer -->
            <p class="text-center text-muted mt-4 small mb-0">
                &copy; {{ date('Y') }} ConnectCare CMS. All rights reserved.
            </p>
        </div>
    </div>
</div>
@endsection
