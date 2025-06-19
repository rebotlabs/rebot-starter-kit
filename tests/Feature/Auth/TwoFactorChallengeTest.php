<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\TwoFactorAuthenticationService;

it('redirects to 2FA challenge when user has 2FA enabled', function () {
    $user = User::factory()->create([
        'two_factor_secret' => encrypt('test-secret'),
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['two_factor_required']);
    $this->assertGuest();
    expect(session('2fa_user_id'))->toBe($user->id);
});

it('allows 2FA verification with correct code', function () {
    $user = User::factory()->create([
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'), // Test secret
        'two_factor_confirmed_at' => now(),
    ]);

    // Simulate having gone through the initial login process
    session(['2fa_user_id' => $user->id, '2fa_remember' => false]);

    $twoFactorService = app(TwoFactorAuthenticationService::class);
    $validCode = $twoFactorService->verifyCode('JBSWY3DPEHPK3PXP', '123456') ? '123456' : '000000';

    // Mock the verification to always return true for this test
    $this->mock(TwoFactorAuthenticationService::class, function ($mock) {
        $mock->shouldReceive('getDecryptedSecret')->andReturn('JBSWY3DPEHPK3PXP');
        $mock->shouldReceive('verifyCode')->andReturn(true);
    });

    $response = $this->post(route('two-factor.login'), [
        'code' => '123456',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
    expect(session('2fa_user_id'))->toBeNull();
});

it('rejects 2FA verification with incorrect code', function () {
    $user = User::factory()->create([
        'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
        'two_factor_confirmed_at' => now(),
    ]);

    session(['2fa_user_id' => $user->id, '2fa_remember' => false]);

    $this->mock(TwoFactorAuthenticationService::class, function ($mock) {
        $mock->shouldReceive('getDecryptedSecret')->andReturn('JBSWY3DPEHPK3PXP');
        $mock->shouldReceive('verifyCode')->andReturn(false);
    });

    $response = $this->post(route('two-factor.login'), [
        'code' => '000000',
    ]);

    $response->assertSessionHasErrors(['code']);
    $this->assertGuest();
});

it('redirects to login if no 2FA session exists', function () {
    $response = $this->get(route('two-factor.login'));

    $response->assertRedirect(route('login'));
});

it('shows 2FA challenge page when session exists', function () {
    $user = User::factory()->create();
    session(['2fa_user_id' => $user->id]);

    $response = $this->get(route('two-factor.login'));

    $response->assertOk();
    $response->assertInertia(function ($page) {
        $page->component('auth/two-factor-challenge');
    });
});
