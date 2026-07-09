<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_login_fails_without_token(): void
    {
        $response = $this->postJson(route('login.google'), []);

        $response->assertStatus(422);
    }

    public function test_google_login_authenticates_new_user_successfully(): void
    {
        // Mock the Firebase lookup response
        Http::fake([
            'https://identitytoolkit.googleapis.com/*' => Http::response([
                'users' => [
                    [
                        'localId' => 'firebase-uid-123',
                        'email' => 'newuser@example.com',
                        'displayName' => 'Google Test User',
                    ]
                ]
            ], 200)
        ]);

        $response = $this->post(route('login.google'), [
            'id_token' => 'valid-firebase-id-token'
        ]);

        // Assert redirect to home
        $response->assertRedirect(route('shop.home'));

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'Google Test User',
            'role' => 'customer',
        ]);

        // Assert user is authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals('newuser@example.com', Auth::user()->email);
    }

    public function test_google_login_authenticates_existing_user_successfully(): void
    {
        // Create an existing user
        $user = new User([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->role = 'customer';
        $user->save();

        // Mock the Firebase lookup response
        Http::fake([
            'https://identitytoolkit.googleapis.com/*' => Http::response([
                'users' => [
                    [
                        'localId' => 'firebase-uid-456',
                        'email' => 'existing@example.com',
                        'displayName' => 'Google Existing User',
                    ]
                ]
            ], 200)
        ]);

        $response = $this->post(route('login.google'), [
            'id_token' => 'valid-firebase-id-token'
        ]);

        // Assert redirect to home
        $response->assertRedirect(route('shop.home'));

        // Assert no duplicate user created (only 1 user exists)
        $this->assertEquals(1, User::where('email', 'existing@example.com')->count());

        // Assert user is authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_google_login_redirects_staff_to_admin_dashboard(): void
    {
        // Create an existing staff user
        $user = new User([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->role = 'admin';
        $user->save();

        // Mock the Firebase lookup response
        Http::fake([
            'https://identitytoolkit.googleapis.com/*' => Http::response([
                'users' => [
                    [
                        'localId' => 'firebase-uid-789',
                        'email' => 'staff@example.com',
                        'displayName' => 'Google Staff User',
                    ]
                ]
            ], 200)
        ]);

        $response = $this->post(route('login.google'), [
            'id_token' => 'valid-firebase-id-token'
        ]);

        // Assert redirect to admin dashboard
        $response->assertRedirect(route('admin.dashboard'));

        // Assert user is authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals('admin', Auth::user()->role);
    }
}
