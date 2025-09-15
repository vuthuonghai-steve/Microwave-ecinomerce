<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendVerificationEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_notice_and_resend(): void
    {
        Bus::fake();
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get('/email/verify')
            ->assertStatus(200)
            ->assertSee('Xác thực email');

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect();

        Bus::assertDispatched(SendVerificationEmail::class);
    }

    public function test_can_verify_email_via_signed_link(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user);

        $url = URL::temporarySignedRoute(
            'verification.verify', now()->addMinutes(60), [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $this->get($url)->assertRedirect('/');
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
