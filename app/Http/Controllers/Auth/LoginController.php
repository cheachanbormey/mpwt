<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * How many failed attempts are allowed before a temporary lockout.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * How long the lockout lasts, in seconds.
     */
    private const DECAY_SECONDS = 60;

    /**
     * Show the login form.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Attempt to sign the user in with session authentication.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ], [], [
            'email' => 'អ៊ីមែល',
            'password' => 'ពាក្យសម្ងាត់',
            'remember' => 'ចងចាំមក',
        ]);

        $throttleKey = $this->throttleKey($request);

        // Too many failures from this address/email pair: lock it out.
        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "ការព្យាយាមចូលច្រើនពេក។ សូមរង់ចាំ {$seconds} វិនាទី ហើយព្យាយាមម្តងទៀត។",
            ]);
        }

        // Passwords are stored hashed, so Auth::attempt compares hashes.
        $remember = $request->boolean('remember');

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            $remember
        )) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);

            throw ValidationException::withMessages([
                'email' => 'អ៊ីមែល ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវ។',
            ]);
        }

        // Successful sign-in clears the lockout counter.
        RateLimiter::clear($throttleKey);

        /*
         * Regenerate the session id so a fixated session id cannot be
         * reused after authentication.
         */
        $request->session()->regenerate();

        // Send the user back to the page they originally asked for.
        return redirect()->intended(route('dashboard'))
            ->with('success', 'ជោគជ័យ។ សូមស្វាគមន៍ '.$request->user()->display_name.'។');
    }

    /**
     * Sign the user out.
     *
     * Only reachable through POST so that a link or an image tag cannot
     * silently end someone's session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalidate the session and issue a fresh CSRF token.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'អ្នកបានចាកចេញពីប្រព័ន្ធ។');
    }

    /**
     * Build the rate limiter key from the IP address and the email.
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower((string) $request->input('email')).'|'.$request->ip()
        );
    }
}
