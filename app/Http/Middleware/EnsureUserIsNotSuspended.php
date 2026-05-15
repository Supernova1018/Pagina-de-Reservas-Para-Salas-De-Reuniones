<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotSuspended
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_suspended) {
            abort(403, 'Tu cuenta está suspendida temporalmente y no puede realizar reservas.');
        }

        return $next($request);
    }
}