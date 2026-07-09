<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = new User([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->role = 'customer';
        $user->save();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertSee('Verify Your Email');
    }

    public function test_verified_user_is_redirected_away_from_verification_screen(): void
    {
        $user = new User([
            'name' => 'Verified User',
            'email' => 'verified@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->role = 'customer';
        $user->email_verified_at = now();
        $user->save();

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertRedirect(route('shop.home'));
    }

    public function test_email_can_be_verified(): void
    {
        $user = new User([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->role = 'customer';
        $user->save();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect(route('shop.home'));
        $response->assertSessionHas('success');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_email_verification_fails_with_invalid_signature(): void
    {
        $user = new User([
            'name' => 'Unverified User',
            'email' => 'unverified@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->role = 'customer';
        $user->save();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Tamper with the URL
        $verificationUrl .= 'tampered';

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertStatus(403);
        $this->assertNull($user->fresh()->email_verified_at);
    }
}
