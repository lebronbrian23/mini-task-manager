<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and validate user role access.
     *
     * Checks if the authenticated user has the required role. If the user is
     * either not authenticated or does not have the specified role, an HTTP
     * 403 error is triggered with an appropriate unauthorized message.
     *
     * @param Request $request The current HTTP request instance.
     * @param Closure $next A callback to pass the request to the next middleware.
     * @param string $role The required role for the requested action.
     * @return Response The HTTP response after processing the request.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the user is unauthorized.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Abort if unauthenticated or the user does NOT have the required role
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            abort(403, 'Unauthorized — Role required: ' . $role);
        }
        return $next($request);
    }
}
