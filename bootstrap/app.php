<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        /*
         | The framework's built-in "health" page ships with a Tailwind CDN
         | script. Because this project must not use Tailwind, the health
         | check is defined manually in routes/web.php and returns JSON.
         */
        // health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * Trust reverse proxies (such as Cloudflare) so HTTPS, client IPs,
         * and host headers are accurately recognized.
         */
        $middleware->trustProxies(at: '*');
        /*
         * Global middleware for the HRMS application.
         * The web group already provides sessions, CSRF and
         * cookie encryption, which flash messages rely on.
         */

        /*
         |----------------------------------------------------------------
         | AUTHENTICATION MIDDLEWARE ALIASES
         |----------------------------------------------------------------
         |
         | "auth"  - built in. Requires a signed-in session.
         | "guest" - RedirectIfAuthenticated. Keeps signed-in users out of
         |           the login screen.
         | "admin" - EnsureUserIsAdmin. 403 for non-admin accounts.
         */
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
            'admin' => EnsureUserIsAdmin::class,
        ]);

        /*
         | Unauthenticated visitors are sent to the login screen instead of
         | Laravel's default "/login" route, which does not exist here.
         */
        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        /*
         |----------------------------------------------------------------
         | ERROR HANDLING STRUCTURE
         |----------------------------------------------------------------
         |
         | Custom error pages live in resources/views/errors/:
         |
         |   403.blade.php  - No permission
         |   404.blade.php  - Page not found
         |   419.blade.php  - Session / CSRF token expired
         |   500.blade.php  - Server error
         |
         | Laravel automatically renders errors/{status}.blade.php when
         | the file exists, so no extra registration is required.
         |
         | 500 is intentionally left to the framework default so the
         | detailed debug page still shows when APP_DEBUG=true.
         |
         | Validation errors keep the Laravel default behaviour
         | (redirect back with $errors available in the view).
         */

        // API and AJAX requests receive JSON instead of an HTML page
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         | Guests hitting a protected route are redirected to the login
         | screen by the "redirectGuestsTo" callback configured in
         | withMiddleware() above.
         |
         | Signed-in staff who open an administrator page receive the 403
         | page in resources/views/errors/403.blade.php, so nothing extra
         | is required here.
         */
    })->create();
