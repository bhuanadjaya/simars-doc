<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role) {
            abort(403);
        }

        if (! in_array($user->role->name, $roles)) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
