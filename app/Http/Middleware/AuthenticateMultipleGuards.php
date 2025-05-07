<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class AuthenticateMultipleGuards
{
    protected $guards = ['admin-api', 'candidate-api', 'employer-api'];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach ($this->guards as $guard) {
            if (auth($guard)->check()) {
                // Set the guard user as the authenticated user
                auth()->shouldUse($guard);
                return $next($request);
            }
        }

        throw new AuthenticationException('Unauthenticated.');
    }
}
