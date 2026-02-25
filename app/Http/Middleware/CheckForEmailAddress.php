<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckForEmailAddress
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        if (auth()->check() && ! auth()->user()->email) {
            return redirect()->route('app.email.create');
        }

        return $next($request);
    }
}
