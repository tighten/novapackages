<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;

class CheckUserClaimedCollaborator
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->route('collaborator')->user->id == auth()->id(), 403);

        return $next($request);
    }
}
