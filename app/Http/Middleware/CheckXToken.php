<?php

namespace App\Http\Middleware;

use Closure;

class CheckXToken
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
        if (null === $request->headers->get('X-TOKEN')) {
            return response(['errors' => ['No X-TOKEN header provided']], 400);
        }

        return $next($request);
    }
}
