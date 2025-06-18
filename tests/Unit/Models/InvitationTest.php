<?php

namespace Tests\Unit\Models;

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    // Relationships tests
    public function test_given_an_invitation_when_accessing_organization_then_it_returns_the_related_organization()
    {
        // Given
        $organization = Organization::factory()->create();
        $invitation = Invitation::factory()->create(['organization_id' => $organization->id]);

        // When
        $relatedOrganization = $invitation->organization;

        // Then
        $this->assertInstanceOf(Organization::class, $relatedOrganization);
        $this->assertEquals($organization->id, $relatedOrganization->id);
    }

    public function test_given_an_invitation_when_accessing_user_then_it_returns_the_related_user_if_exists()
    {
        // Given
        $user = User::factory()->create(['email' => 'test@example.com']);
        $invitation = Invitation::factory()->create(['email' => 'test@example.com']);

        // When
        $relatedUser = $invitation->user;

        // Then
        $this->assertInstanceOf(User::class, $relatedUser);
        $this->assertEquals($user->id, $relatedUser->id);
    }

    public function test_given_an_invitation_when_user_does_not_exist_then_it_returns_null()
    {
        // Given
        $invitation = Invitation::factory()->create(['email' => 'nonexistent@example.com']);

        // When
        $relatedUser = $invitation->user;

        // Then
        $this->assertNull($relatedUser);
    }

    // Factory tests
    public function test_given_invitation_factory_when_creating_invitation_then_it_has_required_attributes()
    {
        // When
        $invitation = Invitation::factory()->create();

        // Then
        $this->assertNotEmpty($invitation->email);
        $this->assertContains($invitation->role, ['admin', 'member']);
        $this->assertEquals('pending', $invitation->status);
        $this->assertNotEmpty($invitation->accept_token);
        $this->assertNotNull($invitation->organization_id);
        // UUID format check - just ensure it's a proper length and format
        $this->assertMatchesRegularExpression('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $invitation->accept_token);
    }

    public function test_given_specific_attributes_when_creating_invitation_then_it_uses_those_attributes()
    {
        // Given
        $organization = Organization::factory()->create();
        $attributes = [
            'email' => 'specific@example.com',
            'role' => 'admin',
            'status' => 'accepted',
            'organization_id' => $organization->id,
        ];

        // When
        $invitation = Invitation::factory()->create($attributes);

        // Then
        $this->assertEquals('specific@example.com', $invitation->email);
        $this->assertEquals('admin', $invitation->role);
        $this->assertEquals('accepted', $invitation->status);
        $this->assertEquals($organization->id, $invitation->organization_id);
    }

    public function test_given_invitation_factory_when_creating_multiple_invitations_then_each_has_unique_token()
    {
        // When
        $invitation1 = Invitation::factory()->create();
        $invitation2 = Invitation::factory()->create();

        // Then
        $this->assertNotEquals($invitation1->accept_token, $invitation2->accept_token);
    }

    // Fillable attributes tests
    public function test_given_an_invitation_when_mass_assigning_fillable_attributes_then_it_accepts_them()
    {
        // Given
        $organization = Organization::factory()->create();
        $attributes = [
            'email' => 'fillable@example.com',
            'role' => 'member',
            'status' => 'pending',
            'organization_id' => $organization->id,
            'accept_token' => Str::uuid(),
        ];

        // When
        $invitation = new Invitation($attributes);

        // Then
        $this->assertEquals('fillable@example.com', $invitation->email);
        $this->assertEquals('member', $invitation->role);
        $this->assertEquals('pending', $invitation->status);
        $this->assertEquals($organization->id, $invitation->organization_id);
        $this->assertEquals($attributes['accept_token'], $invitation->accept_token);
    }

    // Database interactions tests
    public function test_given_invitation_data_when_creating_invitation_then_it_persists_to_database()
    {
        // Given
        $organization = Organization::factory()->create();
        $invitationData = [
            'email' => 'persist@example.com',
            'role' => 'admin',
            'status' => 'pending',
            'organization_id' => $organization->id,
            'accept_token' => Str::uuid(),
        ];

        // When
        $invitation = Invitation::create($invitationData);

        // Then
        $this->assertTrue($invitation->exists);

        $dbInvitation = Invitation::find($invitation->id);
        $this->assertNotNull($dbInvitation);
        $this->assertEquals('persist@example.com', $dbInvitation->email);
        $this->assertEquals('admin', $dbInvitation->role);
    }

    public function test_given_an_invitation_when_updating_status_then_it_persists_changes()
    {
        // Given
        $invitation = Invitation::factory()->create(['status' => 'pending']);

        // When
        $invitation->update(['status' => 'accepted']);

        // Then
        $invitation->refresh();
        $this->assertEquals('accepted', $invitation->status);
    }

    public function test_given_an_invitation_when_soft_deleting_then_it_is_marked_as_deleted()
    {
        // Given
        $invitation = Invitation::factory()->create();

        // When
        $invitation->delete();

        // Then
        $this->assertTrue($invitation->trashed());
        $this->assertNotNull(Invitation::withTrashed()->find($invitation->id));
        $this->assertNull(Invitation::find($invitation->id));
    }

    // Scopes and queries tests
    public function test_given_invitations_with_different_statuses_when_querying_pending_then_it_returns_only_pending()
    {
        // Given
        Invitation::factory()->create(['status' => 'pending']);
        Invitation::factory()->create(['status' => 'accepted']);
        Invitation::factory()->create(['status' => 'rejected']);

        // When
        $pendingInvitations = Invitation::where('status', 'pending')->get();

        // Then
        $this->assertCount(1, $pendingInvitations);
        $this->assertEquals('pending', $pendingInvitations->first()->status);
    }

    public function test_given_invitations_when_querying_by_token_then_it_returns_correct_invitation()
    {
        // Given
        $invitation = Invitation::factory()->create();
        Invitation::factory()->create(); // Another invitation

        // When
        $foundInvitation = Invitation::where('accept_token', $invitation->accept_token)->first();

        // Then
        $this->assertNotNull($foundInvitation);
        $this->assertEquals($invitation->id, $foundInvitation->id);
    }

    public function test_given_invitations_when_querying_by_email_then_it_returns_matching_invitations()
    {
        // Given
        Invitation::factory()->create(['email' => 'target@example.com']);
        Invitation::factory()->create(['email' => 'target@example.com']);
        Invitation::factory()->create(['email' => 'other@example.com']);

        // When
        $matchingInvitations = Invitation::where('email', 'target@example.com')->get();

        // Then
        $this->assertCount(2, $matchingInvitations);
        $this->assertTrue($matchingInvitations->every(fn ($inv) => $inv->email === 'target@example.com'));
    }

    // Role validation tests
    public function test_given_valid_roles_when_creating_invitation_then_it_accepts_them()
    {
        // Given & When
        $adminInvitation = Invitation::factory()->create(['role' => 'admin']);
        $memberInvitation = Invitation::factory()->create(['role' => 'member']);

        // Then
        $this->assertEquals('admin', $adminInvitation->role);
        $this->assertEquals('member', $memberInvitation->role);
    }

    // Timestamps tests
    public function test_given_an_invitation_when_creating_then_it_has_timestamps()
    {
        // When
        $invitation = Invitation::factory()->create();

        // Then
        $this->assertNotNull($invitation->created_at);
        $this->assertNotNull($invitation->updated_at);
    }

    public function test_given_an_invitation_when_updating_then_updated_at_changes()
    {
        // Given
        $invitation = Invitation::factory()->create();
        $originalUpdatedAt = $invitation->updated_at;

        sleep(1); // Ensure time difference

        // When
        $invitation->update(['status' => 'accepted']);

        // Then
        $this->assertTrue($invitation->updated_at->gt($originalUpdatedAt));
    }
}
