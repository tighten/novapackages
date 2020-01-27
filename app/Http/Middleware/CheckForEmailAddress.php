<?php

namespace App\Http\Middleware;

use Closure;

class CheckForEmailAddress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth()->check() && ! auth()->user()->email) {
            return redirect()->route('app.email.create');
        }

        return $next($request);
    }
}
