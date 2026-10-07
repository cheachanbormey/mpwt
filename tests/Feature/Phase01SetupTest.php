<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Phase 01 - Application shell verification.
 *
 * Requires the front-end assets to be built first:
 *     npm run build
 *
 * Since Phase 03 the shell sits behind authentication, so every page test
 * signs in first.
 */
class Phase01SetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_check_endpoint_returns_json(): void
    {
        $response = $this->get('/up');

        $response->assertOk();
        $response->assertJson(['status' => 'ok']);
        $response->assertJsonPath('php', PHP_VERSION);

        // Must start with the major version 13
        $this->assertStringStartsWith('13.', $response->json('laravel'));
    }

    public function test_root_redirects_a_guest_to_the_login_screen(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_root_redirects_a_signed_in_user_to_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/')->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_page_renders_with_the_application_shell(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('main-wrapper', false);    // main area
        $response->assertSee('sidebar', false);         // sidebar
        $response->assertSee('top-navbar', false);      // navbar
        $response->assertSee('sidebarToggle', false);   // navbar toggle button
        $response->assertSee('flash-container', false); // flash message area
    }

    public function test_dashboard_loads_built_vite_assets(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/dashboard');

        $response->assertOk();

        // Vite injects hashed filenames, e.g. /build/assets/app-XXXX.css
        $response->assertSee('/build/assets/app-', false);
    }

    public function test_dashboard_does_not_use_tailwind(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertDontSee('tailwind', false);
        $response->assertDontSee('cdn.jsdelivr.net', false);
    }

    public function test_dashboard_uses_bootstrap_icons(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('bi bi-building', false);
    }

    public function test_missing_page_shows_the_custom_404_view(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/this-page-does-not-exist');

        $response->assertNotFound();
        $response->assertSee('404', false);
        $response->assertSee('error-box', false);
    }

    public function test_flash_success_message_is_displayed(): void
    {
        $this->actingAs(User::factory()->create());

        Route::get('/_test/flash-ok', function () {
            return redirect()->route('dashboard')->with('success', 'បានរក្សាទុកដោយជោគជ័យ');
        })->middleware('web');

        $this->get('/_test/flash-ok')->assertRedirect(route('dashboard'));

        $response = $this->followingRedirects()->get('/_test/flash-ok');

        $response->assertOk();
        $response->assertSee('បានរក្សាទុកដោយជោគជ័យ');
        $response->assertSee('flash-alert', false);
        $response->assertSee('alert-success', false);
    }

    public function test_flash_error_message_is_displayed(): void
    {
        $this->actingAs(User::factory()->create());

        Route::get('/_test/flash-fail', function () {
            return redirect()->route('dashboard')->with('error', 'មិនអាចធ្វើបាន');
        })->middleware('web');

        $response = $this->followingRedirects()->get('/_test/flash-fail');

        $response->assertOk();
        $response->assertSee('មិនអាចធ្វើបាន');
        $response->assertSee('alert-danger', false);
    }

    public function test_validation_errors_are_shown_in_the_flash_area(): void
    {
        $this->actingAs(User::factory()->create());

        Route::post('/_test/invalid', function () {
            $request = request();

            $request->validate([
                'name' => 'required|string|min:5',
            ]);

            return redirect()->route('dashboard');
        })->middleware('web');

        // Posting no data must fail validation and redirect back with errors
        $response = $this->from('/dashboard')->post('/_test/invalid', []);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHasErrors('name');

        // The errors must be visible inside the flash area
        $response = $this->followingRedirects()->from('/dashboard')->post('/_test/invalid', []);

        $response->assertOk();
        $response->assertSee('flash-container', false);
        $response->assertSee('សូមកែកំហុសផងដូចខាងក្រោម៖');
        $response->assertSee('alert-danger', false);
    }

    public function test_error_views_exist_for_the_expected_status_codes(): void
    {
        foreach ([403, 404, 419, 500] as $code) {
            $this->assertFileExists(
                resource_path("views/errors/{$code}.blade.php"),
                "Missing error view for HTTP {$code}"
            );
        }
    }

    public function test_application_uses_the_expected_configuration(): void
    {
        $this->assertSame('Asia/Phnom_Penh', config('app.timezone'));
        $this->assertSame('km', app()->getLocale());
        $this->assertSame('HR Management System', config('app.name'));

        // The test suite forces sqlite, so the MySQL setting is verified
        // against the .env files instead of the runtime config.
        foreach ([base_path('.env'), base_path('.env.example')] as $file) {
            $contents = file_get_contents($file);

            $this->assertMatchesRegularExpression('/^DB_CONNECTION=mysql$/m', $contents, "{$file} must use MySQL");
            $this->assertMatchesRegularExpression('/^DB_DATABASE=hrms$/m', $contents, "{$file} must use the hrms database");
        }
    }
}
