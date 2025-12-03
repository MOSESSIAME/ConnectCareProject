@extends('layouts.guest')

@push('styles')
<style>
/* Hide the floating footer on the right side */
body > p.text-muted {
    display: none !important;
}
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-center min-vh-100 w-100 bg-gradient"
     style="background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);">
    
    <div class="card shadow-lg border-0 rounded-4" style="width: 420px; background: #fff;">
        <div class="card-body p-5">

            <!-- App Header -->
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary mb-1">ConnectCare</h3>
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
                @csrf
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Username</label>
                    <input id="email" type="email" 
                           class="form-control form-control-lg @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required autofocus
                           placeholder="Enter your Username" autocomplete="username">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group input-group-lg">
                        <input id="password" type="password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                               name="password" required placeholder="Enter your password"
                               autocomplete="current-password">
                        <button type="button" class="btn btn-outline-secondary"
                                aria-label="Show password"
                                onclick="
                                    const input = this.previousElementSibling;
                                    const icon  = this.querySelector('i');
                                    const show  = input.type === 'password';
                                    input.type  = show ? 'text' : 'password';
                                    this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                                    icon.classList.toggle('bi-eye', !show);
                                    icon.classList.toggle('bi-eye-slash', show);
                                ">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
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
