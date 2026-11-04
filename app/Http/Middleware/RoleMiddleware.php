<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Get the currently logged-in user
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if account is active
        if (!$user->is_active) {

            auth()->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login');
        }

        // Check the user's role
        if ($user->role !== $role) {
            abort(403, 'Unauthorized access.');
        }

        // Allow request
        return $next($request);
    }
}