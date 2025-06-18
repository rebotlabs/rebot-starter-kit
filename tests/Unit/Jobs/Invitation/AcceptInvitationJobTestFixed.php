<?php

declare(strict_types=1);

use App\Jobs\Invitation\AcceptInvitationJob;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

describe('AcceptInvitationJob', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
        
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'member']);
        
        Notification::fake();
    });

    it('accepts invitation for existing user', function () {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'existing@example.com',
            'user_id' => $existingUser->id,
            'role' => 'member',
        ]);

        $job = new AcceptInvitationJob((string) $invitation->accept_token);
        $result = $job->handle();

        expect($result['success'])->toBeTrue()
            ->and($result['user']->id)->toBe($existingUser->id)
            ->and($result['organization']->id)->toBe($this->organization->id);

        $invitation->refresh();
        expect($invitation->status)->toBe('accepted');

        expect($existingUser->organizations)->toHaveCount(1)
            ->and($existingUser->hasRole('member'))->toBeTrue();
    });

    it('creates new user and accepts invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'newuser@example.com',
            'role' => 'admin',
        ]);

        $userData = [
            'name' => 'New User',
            'password' => 'password123',
        ];

        $job = new AcceptInvitationJob((string) $invitation->accept_token, $userData);
        $result = $job->handle();

        expect($result['success'])->toBeTrue();

        $newUser = User::where('email', 'newuser@example.com')->first();
        expect($newUser)->not->toBeNull()
            ->and($newUser->name)->toBe('New User')
            ->and(Hash::check('password123', $newUser->password))->toBeTrue()
            ->and($newUser->hasRole('admin'))->toBeTrue();

        $invitation->refresh();
        expect($invitation->status)->toBe('accepted');

        Notification::assertSentTo($newUser, EmailVerificationOtpNotification::class);
    });

    it('sets current organization for user without one', function () {
        $existingUser = User::factory()->create(['email' => 'user@example.com']);
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'user@example.com',
            'role' => 'member',
        ]);

        $job = new AcceptInvitationJob((string) $invitation->accept_token);
        $job->handle();

        $existingUser->refresh();
        expect($existingUser->current_organization_id)->toBe($this->organization->id);
    });

    it('does not override existing current organization', function () {
        $anotherOrg = Organization::factory()->create(['owner_id' => $this->user->id]);
        $existingUser = User::factory()->create([
            'email' => 'user@example.com',
            'current_organization_id' => $anotherOrg->id,
        ]);
        
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'user@example.com',
            'role' => 'member',
        ]);

        $job = new AcceptInvitationJob((string) $invitation->accept_token);
        $job->handle();

        $existingUser->refresh();
        expect($existingUser->current_organization_id)->toBe($anotherOrg->id);
    });

    it('does not create duplicate membership', function () {
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        $existingUser->organizations()->attach($this->organization->id, ['role' => 'member']);
        $existingUser->assignRole('member');
        
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'existing@example.com',
            'user_id' => $existingUser->id,
            'role' => 'admin',
        ]);

        $job = new AcceptInvitationJob((string) $invitation->accept_token);
        $result = $job->handle();

        expect($result['success'])->toBeTrue();
        expect($existingUser->organizations)->toHaveCount(1);
    });

    it('throws exception for invalid token', function () {
        expect(fn () => (new AcceptInvitationJob('invalid-token'))->handle())
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws exception for already accepted invitation', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'accepted',
        ]);

        expect(fn () => (new AcceptInvitationJob((string) $invitation->accept_token))->handle())
            ->toThrow(ModelNotFoundException::class);
    });

    it('throws exception when user data is missing for new user', function () {
        $invitation = Invitation::factory()->create([
            'organization_id' => $this->organization->id,
            'email' => 'newuser@example.com',
        ]);

        $job = new AcceptInvitationJob((string) $invitation->accept_token);
        
        expect(fn () => $job->handle())
            ->toThrow('User data is required for new user creation.');
    });
});
