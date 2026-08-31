<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $role = $user->role;

        // Allow if role level >= 50 (admin or super admin)
        // Or if the role has settings.edit permission
        if (!$role || ($role->level < 50 && !in_array('settings.edit', $role->permissions ?? []))) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
