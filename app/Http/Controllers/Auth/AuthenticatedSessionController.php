<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login form.
     */
    public function create(): View|RedirectResponse
    {
        // ✅ Prevent logged-in users from revisiting the login page
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request and redirect based on user role.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ✅ Authenticate user credentials
        $request->authenticate();

        // ✅ Prevent session fixation
        $request->session()->regenerate();

        // ✅ Get the logged-in user and their role
        $user = Auth::user();
        $role = $user->role->name ?? null;

        // ✅ Role-based redirect
        return match ($role) {
            'Admin'           => redirect()->intended(route('admin.dashboard')),
            'Pastor'          => redirect()->intended(route('pastor.dashboard')),
            'Zonal Leader'    => redirect()->intended(route('zone.dashboard')),
            'Homecell Leader' => redirect()->intended(route('homecell.dashboard')),
            'Team Leader'     => redirect()->intended(route('team.dashboard')),
            'Team Member', 
            'Staff'           => redirect()->intended(route('team-member.dashboard')),
            default           => $this->handleInvalidRole($request),
        };
    }

    /**
     * Handle logout and session cleanup.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Handle missing or invalid roles safely.
     */
    private function handleInvalidRole(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();

        return redirect()->route('login')->withErrors([
            'role' => 'Access denied. Your account does not have a valid role assigned. Please contact the administrator.',
        ]);
    }
}
