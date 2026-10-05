<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// ADDED: replaces the role check that each controller method repeated inline.
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            return redirect('/login');
        }

        return $next($request);
    }
}
