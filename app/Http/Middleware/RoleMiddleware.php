<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roleName): Response
    {
        $user = $request->user();

        // tryFrom returns null if the role name (like an error) isn't found
        $requiredRole = UserRole::tryFrom($roleName);

        // Make master an exception to the middleware
        if ($user->role === UserRole::Master) {
            return $next($request);
        }

        // Abort if defined user role doesn't exist
        if (!$user || $user->role !== $requiredRole) {
            abort(403, 'U heeft geen toegang tot deze pagina.');
        }

        return $next($request);
    }
}
