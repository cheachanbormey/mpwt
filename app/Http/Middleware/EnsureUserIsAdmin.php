<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict a route to administrator accounts.
 *
 * Registered under the "admin" alias in bootstrap/app.php:
 *
 *   Route::middleware('admin')->group(...)
 *
 * A signed-in non-admin gets a 403 page; a guest is redirected to the
 * login screen by Laravel's "auth" middleware, which always runs first.
 */
class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->isAdmin()) {
            abort(Response::HTTP_FORBIDDEN, 'ត្រូវការសិទ្ធិអ្នកគ្រប់គ្រង។');
        }

        return $next($request);
    }
}
