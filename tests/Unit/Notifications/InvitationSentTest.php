<?php

namespace Tests\Unit\Notifications;

use App\Models\Invitation;
use App\Models\Organization;
use App\Notifications\InvitationSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class InvitationSentTest extends TestCase
{
    use RefreshDatabase;

    // Via method tests
    public function test_given_an_invitation_when_determining_delivery_channels_then_it_returns_mail_channel()
    {
        // Given
        $invitation = Invitation::factory()->make();
        $notification = new InvitationSent;

        // When
        $channels = $notification->via($invitation);

        // Then
        $this->assertEquals(['mail'], $channels);
    }

    // ToMail method tests
    public function test_given_an_invitation_when_creating_mail_message_then_it_returns_properly_formatted_mail()
    {
        // Given
        $organization = Organization::factory()->create(['name' => 'Test Organization']);
        $invitation = Invitation::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'invited@example.com',
            'role' => 'member',
        ]);
        $notification = new InvitationSent;

        // When
        $mailMessage = $notification->toMail($invitation);

        // Then
        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('You have been invited!', $mailMessage->subject);
        $this->assertEquals('mail.invitation.sent', $mailMessage->markdown);
        $this->assertEquals($invitation->id, $mailMessage->viewData['invitation']->id);
        $this->assertArrayHasKey('invitationUrl', $mailMessage->viewData);

        $invitationUrl = $mailMessage->viewData['invitationUrl'];
        $this->assertStringContainsString($invitation->accept_token, $invitationUrl);
        $this->assertStringContainsString('invitation', $invitationUrl);
    }

    public function test_given_an_invitation_when_creating_mail_message_then_the_invitation_ur_l_is_properly_signed()
    {
        // Given
        $organization = Organization::factory()->create();
        $invitation = Invitation::factory()->create([
            'organization_id' => $organization->id,
        ]);
        $notification = new InvitationSent;

        // When
        $mailMessage = $notification->toMail($invitation);
        $invitationUrl = $mailMessage->viewData['invitationUrl'];

        // Then
        $this->assertStringContainsString('signature=', $invitationUrl);
        $this->assertStringContainsString('expires=', $invitationUrl);
    }

    public function test_given_invitations_with_different_tokens_when_creating_mail_messages_then_each_gets_unique_url()
    {
        // Given
        $organization = Organization::factory()->create();
        $invitation1 = Invitation::factory()->create(['organization_id' => $organization->id]);
        $invitation2 = Invitation::factory()->create(['organization_id' => $organization->id]);
        $notification = new InvitationSent;

        // When
        $mailMessage1 = $notification->toMail($invitation1);
        $mailMessage2 = $notification->toMail($invitation2);

        // Then
        $url1 = $mailMessage1->viewData['invitationUrl'];
        $url2 = $mailMessage2->viewData['invitationUrl'];

        $this->assertNotEquals($url1, $url2);
        $this->assertStringContainsString($invitation1->accept_token, $url1);
        $this->assertStringContainsString($invitation2->accept_token, $url2);
    }

    // ToArray method tests
    public function test_given_an_invitation_when_converting_to_array_then_it_returns_invitation_data()
    {
        // Given
        $organization = Organization::factory()->create();
        $invitation = Invitation::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'invited@example.com',
            'role' => 'admin',
        ]);
        $notification = new InvitationSent;

        // When
        $array = $notification->toArray($invitation);

        // Then
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('email', $array);
        $this->assertArrayHasKey('role', $array);
        $this->assertArrayHasKey('organization_id', $array);
        $this->assertEquals('invited@example.com', $array['email']);
        $this->assertEquals('admin', $array['role']);
        $this->assertEquals($organization->id, $array['organization_id']);
    }

    public function test_given_different_invitations_when_converting_to_array_then_each_returns_correct_data()
    {
        // Given
        $organization = Organization::factory()->create();
        $invitation1 = Invitation::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'user1@example.com',
            'role' => 'member',
        ]);
        $invitation2 = Invitation::factory()->create([
            'organization_id' => $organization->id,
            'email' => 'user2@example.com',
            'role' => 'admin',
        ]);
        $notification = new InvitationSent;

        // When
        $array1 = $notification->toArray($invitation1);
        $array2 = $notification->toArray($invitation2);

        // Then
        $this->assertEquals('user1@example.com', $array1['email']);
        $this->assertEquals('member', $array1['role']);
        $this->assertEquals('user2@example.com', $array2['email']);
        $this->assertEquals('admin', $array2['role']);
    }

    // Constructor tests
    public function test_given_no_parameters_when_creating_notification_then_it_constructs_successfully()
    {
        // When
        $notification = new InvitationSent;

        // Then
        $this->assertInstanceOf(InvitationSent::class, $notification);
    }
}
