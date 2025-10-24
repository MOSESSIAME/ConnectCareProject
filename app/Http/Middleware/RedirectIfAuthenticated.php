<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * If the user is already logged in and tries to visit /login or /register,
     * redirect them to their role-based dashboard.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                $role = $user->role->name ?? '';

                return match ($role) {
                    'Admin'           => redirect('/admin/dashboard'),
                    'Pastor'          => redirect('/pastor/dashboard'),
                    'Zonal Leader'    => redirect('/zone/dashboard'),
                    'Homecell Leader' => redirect('/homecell/dashboard'),
                    'Team Leader'     => redirect('/team/dashboard'),
                    'Team Member', 'Staff' => redirect('/member/dashboard'),
                    default           => redirect('/'),
                };
            }
        }

        return $next($request);
    }
}
