<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;

class CheckRole
{
    /**
     * Handle the incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || ! $request->user()->role == $role) {
            return redirect('/');
        }

        return $next($request);
    }
}
