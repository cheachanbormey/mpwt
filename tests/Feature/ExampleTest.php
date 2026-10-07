<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * "/" sends a guest to the login screen.
     */
    public function test_the_root_url_redirects_to_the_login_screen(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    /**
     * A signed-in account lands on the dashboard.
     */
    public function test_the_dashboard_page_loads_successfully(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/dashboard')->assertStatus(200);
    }
}
