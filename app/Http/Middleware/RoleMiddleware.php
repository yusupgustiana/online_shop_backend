<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ambil role user + normalisasi
        $userRole = strtoupper(trim(Auth::user()->roles ?? ''));

        // Normalisasi role dari route
        $roles = array_map(function ($role) {
            return strtoupper(trim($role));
        }, $roles);

        // Debug (optional)
        \Log::info('ROLE CHECK', [
            'user_role' => $userRole,
            'allowed_roles' => $roles
        ]);

        if (!in_array($userRole, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}