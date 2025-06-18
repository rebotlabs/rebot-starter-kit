<?php

declare(strict_types=1);

use App\Jobs\Organization\DeleteOrganizationJob;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->organization = Organization::factory()->create(['owner_id' => $this->owner->id]);
});

it('deletes the organization', function () {
    expect(Organization::where('id', $this->organization->id)->exists())->toBeTrue();

    $job = new DeleteOrganizationJob($this->organization, $this->owner);
    $job->handle();

    expect(Organization::where('id', $this->organization->id)->exists())->toBeFalse();
});

it('soft deletes the organization if using soft deletes', function () {
    $organizationId = $this->organization->id;

    $job = new DeleteOrganizationJob($this->organization, $this->owner);
    $job->handle();

    // Check if organization is deleted from main query
    expect(Organization::where('id', $organizationId)->exists())->toBeFalse();

    // If soft deletes are enabled, check trashed
    if (method_exists(Organization::class, 'withTrashed')) {
        expect(Organization::withTrashed()->where('id', $organizationId)->exists())->toBeTrue();
    }
});
