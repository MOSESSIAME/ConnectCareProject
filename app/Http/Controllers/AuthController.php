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
     * Show login page
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // ✅ Role-based redirect
        switch ($user->role->name ?? '') {
            case 'Admin':
                return redirect()->intended('/admin/dashboard');
            case 'Pastor':
                return redirect()->intended('/pastor/dashboard');
            case 'Zonal Leader':
                return redirect()->intended('/zone/dashboard');
            case 'Homecell Leader':
                return redirect()->intended('/homecell/dashboard');
            case 'Team Leader':
                return redirect()->intended('/team/dashboard');
            case 'Team Member':
            case 'Staff':
                return redirect()->intended('/member/dashboard');
            default:
                return redirect()->intended('/');
        }
    }

    /**
     * Logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
