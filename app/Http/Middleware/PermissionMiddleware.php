<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handles an incoming request by checking user permissions.
     *
     * @param Request $request The incoming request instance.
     * @param Closure $next The next middleware to be executed.
     * @param string $permission The required permission to access the resource.
     *
     * @return Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException Throws an HTTP 403 exception if the user lacks the required permission.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Abort if unauthenticated or the user does NOT have the required permission
        if (! $request->user() || ! $request->user()->hasPermission($permission)) {
            abort(403, 'Access denied — Missing permission: ' . $permission);
        }

        return $next($request);
    }
}
