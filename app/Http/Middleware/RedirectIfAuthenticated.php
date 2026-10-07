<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keep signed-in users away from guest-only pages such as the login form.
 *
 * Registered under the "guest" alias in bootstrap/app.php.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
