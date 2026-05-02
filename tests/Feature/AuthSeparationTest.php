<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AuthSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    public function test_admin_can_login_via_admin_login_route(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_non_admin_cannot_login_via_admin_login_route(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'is_admin' => false,
        ]);

        $response = $this->from(route('admin.login'))->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_customer_can_still_login_via_customer_login_route(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
            'is_admin' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_login_via_customer_login_route(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin-via-customer@example.com',
            'is_admin' => true,
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_customer_login_page_renders_invalid_credentials_message(): void
    {
        $response = $this->followingRedirects()->post(route('login'), [
            'email' => 'missing@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertOk();
        $response->assertSeeText('The provided credentials do not match our records.');
    }

    public function test_admin_google_login_is_blocked(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin-google@example.com',
            'is_admin' => true,
        ]);

        $googleUser = Mockery::mock();
        $googleUser->shouldReceive('getEmail')->andReturn($admin->email);
        $googleUser->shouldReceive('getName')->andReturn('Admin Google');
        $googleUser->shouldReceive('getId')->andReturn('google-admin-id');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.png');

        Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
        Socialite::shouldReceive('stateless')->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($googleUser);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect('/login');
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_admin_password_reset_routes_are_available(): void
    {
        $response = $this->get(route('admin.password.request'));
        $response->assertOk();

        $admin = User::factory()->create([
            'email' => 'admin-reset@example.com',
            'is_admin' => true,
        ]);

        $resetResponse = $this->post(route('admin.password.email'), [
            'email' => $admin->email,
        ]);

        $resetResponse->assertSessionHas('success');
    }

    public function test_customer_password_reset_route_still_available(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertOk();
    }

    public function test_admin_routes_remain_inaccessible_to_non_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertForbidden();
    }

    public function test_guest_visiting_admin_dashboard_is_redirected_to_admin_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_customer_visiting_admin_login_is_redirected_home(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('admin.login'));
        $response->assertRedirect('/');
    }

    public function test_authenticated_admin_visiting_customer_login_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('login'));
        $response->assertRedirect(route('admin.dashboard'));
    }
}
