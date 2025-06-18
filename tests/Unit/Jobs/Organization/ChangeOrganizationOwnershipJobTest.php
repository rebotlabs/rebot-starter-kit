<?php

declare(strict_types=1);

use App\Jobs\Organization\ChangeOrganizationOwnershipJob;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->currentOwner = User::factory()->create();
    $this->newOwner = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->currentOwner->id]);
    $this->member = Member::factory()->create([
        'organization_id' => $this->organization->id,
        'user_id' => $this->newOwner->id,
    ]);
});

it('changes organization ownership to the specified member', function () {
    $job = new ChangeOrganizationOwnershipJob($this->organization, $this->member->id);
    $result = $job->handle();

    expect($result)->toBeInstanceOf(Organization::class);
    expect($result->owner_id)->toBe($this->newOwner->id);

    $this->organization->refresh();
    expect($this->organization->owner_id)->toBe($this->newOwner->id);
});

it('throws exception when member does not exist', function () {
    $nonExistentMemberId = 99999;

    expect(fn () => (new ChangeOrganizationOwnershipJob($this->organization, $nonExistentMemberId))->handle())
        ->toThrow('No query results for model [App\Models\Member]');
});

it('returns the updated organization', function () {
    $job = new ChangeOrganizationOwnershipJob($this->organization, $this->member->id);
    $result = $job->handle();

    expect($result)->toBeInstanceOf(Organization::class);
    expect($result->id)->toBe($this->organization->id);
    expect($result->owner_id)->toBe($this->newOwner->id);
});
