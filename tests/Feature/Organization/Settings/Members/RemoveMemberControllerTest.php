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

describe('RemoveMemberController', function () {
    it('removes a member from the organization', function () {
        expect($this->organization->members()->count())->toBe(1);

        $this->actingAs($this->user)
            ->delete(route('organization.settings.members.remove', [$this->organization, $this->memberRecord]))
            ->assertRedirect()
            ->assertSessionHas('success', 'Member removed successfully.');

        expect($this->organization->members()->count())->toBe(0);
    });

    it('prevents removing the organization owner', function () {
        $ownerMember = Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $this->actingAs($this->user)
            ->delete(route('organization.settings.members.remove', [$this->organization, $ownerMember]))
            ->assertRedirect()
            ->assertSessionHasErrors(['error' => 'Cannot remove the organization owner.']);

        expect($this->organization->members()->count())->toBe(2); // original member + owner member
    });

    it('clears current organization if member is leaving their current organization', function () {
        expect($this->member->current_organization_id)->toBe($this->organization->id);

        $this->actingAs($this->user)
            ->delete(route('organization.settings.members.remove', [$this->organization, $this->memberRecord]))
            ->assertRedirect();

        $this->member->refresh();
        expect($this->member->current_organization_id)->toBeNull();
    });

    it('handles member with multiple organization memberships', function () {
        $anotherOrganization = Organization::factory()->create();
        Member::factory()->create([
            'user_id' => $this->member->id,
            'organization_id' => $anotherOrganization->id,
        ]);

        $this->actingAs($this->user)
            ->delete(route('organization.settings.members.remove', [$this->organization, $this->memberRecord]))
            ->assertRedirect();

        $this->member->refresh();
        expect($this->member->current_organization_id)->toBe($anotherOrganization->id);
        expect($this->member->organizations()->count())->toBe(1);
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
            ->delete(route('organization.settings.members.remove', [$this->organization, $this->memberRecord]))
            ->assertRedirect(route('organization.settings.leave', $this->organization));
    });

    it('requires authentication', function () {
        $this->delete(route('organization.settings.members.remove', [$this->organization, $this->memberRecord]))
            ->assertRedirect(route('login'));
    });
});
