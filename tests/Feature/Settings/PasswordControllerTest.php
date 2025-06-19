<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
    $this->user->update(['current_organization_id' => $this->organization->id]);
});

describe('ShowPasswordController', function () {
    it('displays the security settings page', function () {
        $response = $this->actingAs($this->user)
            ->get(route('settings.security'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/security')
            );
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('settings.security'));

        $response->assertRedirect(route('login'));
    });
});

describe('UpdatePasswordController', function () {
    it('updates password with valid data', function () {
        $response = $this->actingAs($this->user)
            ->put(route('settings.password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect();

        $this->user->refresh();
        expect(Hash::check('new-password', $this->user->password))->toBeTrue();
        expect(Hash::check('password', $this->user->password))->toBeFalse();
    });

    it('validates current password is correct', function () {
        $response = $this->actingAs($this->user)
            ->put(route('settings.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertSessionHasErrors(['current_password']);
    });

    it('validates new password confirmation matches', function () {
        $response = $this->actingAs($this->user)
            ->put(route('settings.password.update'), [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'different-password',
            ]);

        $response->assertSessionHasErrors(['password']);
    });

    it('validates password meets minimum length requirement', function () {
        $response = $this->actingAs($this->user)
            ->put(route('settings.password.update'), [
                'current_password' => 'password',
                'password' => '123',
                'password_confirmation' => '123',
            ]);

        $response->assertSessionHasErrors(['password']);
    });

    it('validates required fields', function () {
        $response = $this->actingAs($this->user)
            ->put(route('settings.password.update'), []);

        $response->assertSessionHasErrors(['current_password', 'password']);
    });
});
