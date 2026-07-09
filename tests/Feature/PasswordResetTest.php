<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->role = 'customer';
        $user->save();

        $response = $this->post(route('password.email'), [
            'email' => 'user@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    public function test_reset_password_page_can_be_rendered_with_token(): void
    {
        $token = 'dummy-token';

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => 'user@example.com']));

        $response->assertStatus(200);
        $response->assertSee('Reset Password');
        $response->assertSee('dummy-token');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('old-password'),
        ]);
        $user->role = 'customer';
        $user->save();

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }
}
