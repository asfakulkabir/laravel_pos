<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * Supports multiple roles, e.g. role:admin,moderator
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role, string ...$roles): Response
    {
        $allowed = array_map('trim', array_merge([$role], $roles));

        if (!$request->user() || ! in_array($request->user()->role, $allowed, true)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
