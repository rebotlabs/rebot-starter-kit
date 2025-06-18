<?php

declare(strict_types=1);

use App\Jobs\Member\LeaveMembershipJob;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->owner->id]);
    $this->member = Member::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->user->id,
    ]);
});

it('allows a member to leave an organization', function () {
    expect(Member::where('user_id', $this->user->id)->exists())->toBeTrue();

    $job = new LeaveMembershipJob($this->organization, $this->user);
    $job->handle();

    expect(Member::where('user_id', $this->user->id)->exists())->toBeFalse();
});

it('dissociates current organization if user is leaving their current organization', function () {
    $this->user->update(['current_organization_id' => $this->organization->id]);

    $job = new LeaveMembershipJob($this->organization, $this->user);
    $job->handle();

    $this->user->refresh();
    expect($this->user->current_organization_id)->toBeNull();
});

it('throws exception when organization owner tries to leave', function () {
    expect(fn () => (new LeaveMembershipJob($this->organization, $this->owner))->handle())
        ->toThrow(Exception::class, 'Organization owners cannot leave their organization. You must transfer ownership first.');
});

it('throws exception when user is not a member of the organization', function () {
    $nonMember = User::factory()->create();

    expect(fn () => (new LeaveMembershipJob($this->organization, $nonMember))->handle())
        ->toThrow(Exception::class, 'User is not a member of this organization.');
});

it('does not affect current organization if user is leaving a different organization', function () {
    $anotherOrganization = Organization::factory()->create(['owner_id' => $this->owner->id]);
    $this->user->update(['current_organization_id' => $anotherOrganization->id]);

    $job = new LeaveMembershipJob($this->organization, $this->user);
    $job->handle();

    $this->user->refresh();
    expect($this->user->current_organization_id)->toBe($anotherOrganization->id);
});
