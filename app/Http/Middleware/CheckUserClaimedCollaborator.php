<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserClaimedCollaborator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_unless($request->route('collaborator')->user->id == auth()->id(), 403);

        return $next($request);
    }
}
