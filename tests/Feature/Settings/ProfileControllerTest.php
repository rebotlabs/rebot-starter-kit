<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
    $this->user->update(['current_organization_id' => $this->organization->id]);
});

describe('ShowProfileController', function () {
    it('displays the profile settings page', function () {
        $response = $this->actingAs($this->user)
            ->get(route('settings.profile'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('settings/profile')
                ->has('auth.user')
            );
    });

    it('redirects unauthenticated users to login', function () {
        $response = $this->get(route('settings.profile'));

        $response->assertRedirect(route('login'));
    });
});

describe('UpdateProfileController', function () {
    it('updates profile with valid data', function () {
        $profileData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $response = $this->actingAs($this->user)
            ->patch(route('settings.profile'), $profileData);

        $response->assertRedirect(route('settings.profile'));

        $this->user->refresh();
        expect($this->user->name)->toBe('Updated Name');
        expect($this->user->email)->toBe('updated@example.com');
        expect($this->user->email_verified_at)->toBeNull(); // Should reset when email changes
    });

    it('preserves email verification when email unchanged', function () {
        $originalVerifiedAt = $this->user->email_verified_at;

        $response = $this->actingAs($this->user)
            ->patch(route('settings.profile'), [
                'name' => 'Updated Name',
                'email' => $this->user->email,
            ]);

        $response->assertRedirect(route('settings.profile'));

        $this->user->refresh();
        expect($this->user->email_verified_at)->toEqual($originalVerifiedAt);
    });

    it('validates required fields', function () {
        $response = $this->actingAs($this->user)
            ->patch(route('settings.profile'), []);

        $response->assertSessionHasErrors(['name', 'email']);
    });

    it('validates email format', function () {
        $response = $this->actingAs($this->user)
            ->patch(route('settings.profile'), [
                'name' => 'Test User',
                'email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors(['email']);
    });

    it('validates unique email', function () {
        $otherUser = User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->actingAs($this->user)
            ->patch(route('settings.profile'), [
                'name' => 'Test User',
                'email' => 'taken@example.com',
            ]);

        $response->assertSessionHasErrors(['email']);
    });
});

describe('DeleteAccountController', function () {
    it('deletes account with correct password', function () {
        $response = $this->actingAs($this->user)
            ->delete(route('settings.profile'), [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');
        expect(User::find($this->user->id))->toBeNull();
        $this->assertGuest();
    });

    it('prevents deletion with incorrect password', function () {
        $response = $this->actingAs($this->user)
            ->delete(route('settings.profile'), [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors(['password']);
        expect(User::find($this->user->id))->not->toBeNull();
    });

    it('validates password is required', function () {
        $response = $this->actingAs($this->user)
            ->delete(route('settings.profile'), []);

        $response->assertSessionHasErrors(['password']);
    });
});
