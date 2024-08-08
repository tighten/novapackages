<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;

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
