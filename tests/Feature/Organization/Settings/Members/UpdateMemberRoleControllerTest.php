<?php

use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->user->id]);
    $this->user->update(['current_organization_id' => $this->organization->id]);
    $this->user->assignRole('admin');

    // Create roles if they don't exist
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'member']);

    $this->member = User::factory()->create();
    $this->memberRecord = Member::factory()->create([
        'user_id' => $this->member->id,
        'organization_id' => $this->organization->id,
    ]);
    $this->member->assignRole('member');
    $this->member->update(['current_organization_id' => $this->organization->id]);
});

describe('UpdateMemberRoleController', function () {
    it('updates member role from member to admin', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.members.update-role', [$this->organization, $this->memberRecord]), [
                'role' => 'admin',
            ])
            ->assertRedirect();

        $this->member->refresh();
        expect($this->member->hasRole('admin'))->toBeTrue();
        expect($this->member->hasRole('member'))->toBeFalse();
    });

    it('updates member role from admin to member', function () {
        $this->member->syncRoles(['admin']);

        $this->actingAs($this->user)
            ->patch(route('organization.settings.members.update-role', [$this->organization, $this->memberRecord]), [
                'role' => 'member',
            ])
            ->assertRedirect();

        $this->member->refresh();
        expect($this->member->hasRole('member'))->toBeTrue();
        expect($this->member->hasRole('admin'))->toBeFalse();
    });

    it('validates role is required', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.members.update-role', [$this->organization, $this->memberRecord]), [])
            ->assertSessionHasErrors(['role']);
    });

    it('validates role is valid', function () {
        $this->actingAs($this->user)
            ->patch(route('organization.settings.members.update-role', [$this->organization, $this->memberRecord]), [
                'role' => 'invalid-role',
            ])
            ->assertSessionHasErrors(['role']);
    });

    it('requires admin permissions', function () {
        $regularMember = User::factory()->create();
        Member::factory()->create([
            'user_id' => $regularMember->id,
            'organization_id' => $this->organization->id,
        ]);
        $regularMember->assignRole('member');
        $regularMember->update(['current_organization_id' => $this->organization->id]);

        $this->actingAs($regularMember)
            ->patch(route('organization.settings.members.update-role', [$this->organization, $this->memberRecord]), [
                'role' => 'admin',
            ])
            ->assertRedirect(route('organization.settings.leave', $this->organization));
    });

    it('requires authentication', function () {
        $this->patch(route('organization.settings.members.update-role', [$this->organization, $this->memberRecord]), [
            'role' => 'admin',
        ])
            ->assertRedirect(route('login'));
    });
});
