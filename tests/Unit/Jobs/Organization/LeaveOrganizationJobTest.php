<?php

declare(strict_types=1);

use App\Jobs\Organization\LeaveOrganizationJob;
use App\Models\Member;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('LeaveOrganizationJob', function () {
    beforeEach(function () {
        $this->owner = User::factory()->create();
        $this->user = User::factory()->create();
        $this->organization = Organization::factory()->create(['owner_id' => $this->owner->id]);
        $this->anotherOrganization = Organization::factory()->create(['owner_id' => $this->owner->id]);
    });

    it('removes user from organization successfully', function () {
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $job = new LeaveOrganizationJob($this->user, $this->organization);
        $result = $job->handle();

        $member = $this->organization->members()->where('user_id', $this->user->id)->first();
        expect($member)->toBeNull();

        expect($result['user']->id)->toBe($this->user->id)
            ->and($result['organizationsCount'])->toBe(0)
            ->and($result['nextOrganization'])->toBeNull();
    });

    it('clears current organization when leaving current org', function () {
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $this->user->currentOrganization()->associate($this->organization)->save();

        $job = new LeaveOrganizationJob($this->user, $this->organization);
        $result = $job->handle();

        $this->user->refresh();
        expect($this->user->current_organization_id)->toBeNull();
    });

    it('does not clear current organization when leaving non-current org', function () {
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->anotherOrganization->id,
        ]);

        $this->user->currentOrganization()->associate($this->anotherOrganization)->save();

        $job = new LeaveOrganizationJob($this->user, $this->organization);
        $result = $job->handle();

        $this->user->refresh();
        expect($this->user->current_organization_id)->toBe($this->anotherOrganization->id);
    });

    it('returns correct organizations count and next organization', function () {
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->anotherOrganization->id,
        ]);

        $job = new LeaveOrganizationJob($this->user, $this->organization);
        $result = $job->handle();

        expect($result['organizationsCount'])->toBe(1)
            ->and($result['nextOrganization']->id)->toBe($this->anotherOrganization->id);
    });

    it('handles user not being member of organization', function () {
        $job = new LeaveOrganizationJob($this->user, $this->organization);
        $result = $job->handle();

        expect($result['organizationsCount'])->toBe(0)
            ->and($result['nextOrganization'])->toBeNull()
            ->and($result['user']->id)->toBe($this->user->id);
    });

    it('returns empty result when user has no remaining organizations', function () {
        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        $job = new LeaveOrganizationJob($this->user, $this->organization);
        $result = $job->handle();

        expect($result['organizationsCount'])->toBe(0)
            ->and($result['nextOrganization'])->toBeNull();
    });

    it('handles multiple memberships correctly', function () {
        $thirdOrganization = Organization::factory()->create(['owner_id' => $this->owner->id]);

        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->organization->id,
        ]);

        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $this->anotherOrganization->id,
        ]);

        Member::factory()->create([
            'user_id' => $this->user->id,
            'organization_id' => $thirdOrganization->id,
        ]);

        $job = new LeaveOrganizationJob($this->user, $this->organization);
        $result = $job->handle();

        expect($result['organizationsCount'])->toBe(2)
            ->and($result['nextOrganization'])->not->toBeNull()
            ->and($result['nextOrganization']->id)->toBeIn([$this->anotherOrganization->id, $thirdOrganization->id]);
    });
});
