<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Phase 03 - login, session handling and logout.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | Login screen
    |--------------------------------------------------------------------------
    */

    public function test_the_login_screen_can_be_reached(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('ចូលប្រព័ន្ធ');
    }

    public function test_the_root_url_sends_a_guest_to_the_login_screen(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    /*
    |--------------------------------------------------------------------------
    | Signing in
    |--------------------------------------------------------------------------
    */

    public function test_an_admin_can_sign_in_with_valid_credentials(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'chief@example.com',
            'password' => 'correct-horse',
        ]);

        $response = $this->post('/login', [
            'email' => 'chief@example.com',
            'password' => 'correct-horse',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_a_staff_member_can_sign_in(): void
    {
        $user = User::factory()->staff()->create([
            'email' => 'clerk@example.com',
            'password' => 'correct-horse',
        ]);

        $this->post('/login', [
            'email' => 'clerk@example.com',
            'password' => 'correct-horse',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame(User::ROLE_STAFF, $user->fresh()->role);
    }

    public function test_the_password_is_stored_hashed_and_never_in_plain_text(): void
    {
        User::factory()->create(['password' => 'plaintext-secret']);

        $stored = User::query()->value('password');

        $this->assertNotSame('plaintext-secret', $stored);
        $this->assertTrue(password_verify('plaintext-secret', $stored));
    }

    public function test_signing_in_wrong_credentials_fails_without_authenticating(): void
    {
        User::factory()->create([
            'email' => 'clerk@example.com',
            'password' => 'correct-horse',
        ]);

        $this->post('/login', [
            'email' => 'clerk@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_unknown_email_is_rejected(): void
    {
        $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'whatever',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_signing_in_requires_an_email_and_a_password(): void
    {
        $this->post('/login', [])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_a_malformed_email_is_rejected(): void
    {
        $this->post('/login', [
            'email' => 'not-an-email',
            'password' => 'correct-horse',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_signed_in_account_cannot_see_the_login_screen_again(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/login')->assertRedirect(route('dashboard'));
    }

    public function test_repeated_failures_are_throttled(): void
    {
        User::factory()->create([
            'email' => 'clerk@example.com',
            'password' => 'correct-horse',
        ]);

        // The controller allows five attempts per minute per email + IP.
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post('/login', [
                'email' => 'clerk@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'clerk@example.com',
            'password' => 'correct-horse',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /*
    |--------------------------------------------------------------------------
    | Signed-in only pages
    |--------------------------------------------------------------------------
    */

    public function test_guests_are_redirected_to_the_login_screen(): void
    {
        foreach (['/dashboard', '/profile', '/attendance', '/leave-requests', '/meetings'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_a_signed_in_user_can_reach_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/dashboard')->assertOk()->assertViewIs('dashboard.index');
    }

    /*
    |--------------------------------------------------------------------------
    | Signing out
    |--------------------------------------------------------------------------
    */

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_logout_is_not_available_with_a_get_request(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/logout')->assertStatus(405);

        // The session must survive a failed attempt.
        $this->assertTrue(Auth::check());
    }

    public function test_a_guest_cannot_post_to_logout(): void
    {
        $this->post('/logout')->assertRedirect(route('login'));
    }
}
