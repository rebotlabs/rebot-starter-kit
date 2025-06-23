<?php

declare(strict_types=1);

use App\Actions\Organization\UpdateOrganizationAction;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->organization = Organization::factory()->create([
        'owner_id' => $this->owner->id,
        'name' => 'Original Name',
    ]);
    $this->action = new UpdateOrganizationAction;
});

it('updates organization with provided data', function () {
    $data = [
        'name' => 'Updated Organization Name',
    ];

    $result = $this->action->execute(organization: $this->organization, data: $data);

    expect($result)->toBeInstanceOf(Organization::class);
    expect($result->name)->toBe('Updated Organization Name');

    $this->organization->refresh();
    expect($this->organization->name)->toBe('Updated Organization Name');
});

it('updates multiple fields at once', function () {
    $data = [
        'name' => 'New Name',
        'slug' => 'new-slug',
    ];

    $result = $this->action->execute(organization: $this->organization, data: $data);

    $this->organization->refresh();
    expect($this->organization->name)->toBe('New Name');
    expect($this->organization->slug)->toBe('new-slug');
});

it('returns the updated organization instance', function () {
    $data = ['name' => 'Updated Name'];

    $result = $this->action->execute(organization: $this->organization, data: $data);

    expect($result)->toBeInstanceOf(Organization::class);
    expect($result->id)->toBe($this->organization->id);
    expect($result->name)->toBe('Updated Name');
});

it('handles empty data array gracefully', function () {
    $originalName = $this->organization->name;
    $data = [];

    $result = $this->action->execute(organization: $this->organization, data: $data);

    $this->organization->refresh();
    expect($this->organization->name)->toBe($originalName);
    expect($result)->toBeInstanceOf(Organization::class);
});
