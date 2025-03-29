<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the login page is accessible.
     */
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Remember Me');
        $response->assertSee('Forgot Password?');
    }

    /**
     * Test that a registered user can log in with correct credentials.
     */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('ValidPassword123') // Create user with known password
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'ValidPassword123',
        ]);

        $response->assertRedirect('/dashboard'); // Ensure successful login redirects to dashboard
        $this->assertAuthenticatedAs($user); // Assert the user is authenticated
    }

    /**
     * Test that incorrect credentials return an error message.
     */
    public function test_login_fails_with_incorrect_credentials()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(302); // Redirect back to login page
        $response->assertSessionHasErrors(['email']); // Ensure validation error for email
        $this->assertGuest(); // Ensure user is not authenticated
    }

    /**
     * Test that locking a user account after multiple failed login attempts works.
     */
    public function test_account_is_locked_after_multiple_failed_attempts()
    {
        RateLimiter::clear('login');

        $user = User::factory()->create([
            'password' => bcrypt('ValidPassword123')
        ]);

        // Fail login 5 times in quick succession
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'WrongPassword',
            ]);
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'ValidPassword123',
        ]);

        $response->assertStatus(429); // Too Many Requests
        $this->assertGuest(); // Ensure user is not authenticated
    }

    /**
     * Test that the "remember me" functionality keeps the user logged in.
     */
    public function test_remember_me_keeps_user_logged_in()
    {
        $user = User::factory()->create([
            'password' => bcrypt('ValidPassword123')
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'ValidPassword123',
            'remember' => true,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // Simulate 30-day session persistence
        $this->travel(29)->days();
        $this->assertAuthenticatedAs($user); // The user should still be authenticated
    }

    /**
     * Test that "Forgot Password?" link redirects to the password reset page.
     */
    public function test_forgot_password_link_redirects_to_reset_page()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200); // Ensure password reset page is accessible
        $response->assertSee('Reset Password'); // Check for reset password text
    }

    /**
     * Test that users are logged out after being idle for 30 minutes.
     */
    public function test_user_is_logged_out_after_session_timeout()
    {
        $this->withoutMiddleware();

        $user = User::factory()->create([
            'password' => bcrypt('ValidPassword123')
        ]);

        // Login
        $this->actingAs($user);

        // Simulate 30-minute inactivity
        $this->travel(31)->minutes();
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login'); // Ensure user is redirected to login page
        $this->assertGuest(); // Ensure user is logged out
    }

    /**
     * Test that the logout process works correctly.
     */
    public function test_user_can_log_out()
    {
        $user = User::factory()->create([
            'password' => bcrypt('ValidPassword123')
        ]);

        $this->actingAs($user);
        $this->post('/logout')->assertRedirect('/login'); // Log out and redirect to login

        $this->assertGuest(); // Ensure no one is logged in
    }
}