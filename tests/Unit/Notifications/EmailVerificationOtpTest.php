<?php

namespace Tests\Unit\Notifications;

use App\Models\User;
use App\Notifications\EmailVerificationOtpNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;
use Tests\TestCase;

class EmailVerificationOtpTest extends TestCase
{
    use RefreshDatabase;

    // Constructor tests
    public function test_given_an_ot_p_code_when_creating_notification_then_it_stores_the_code()
    {
        // Given
        $otpCode = '123456';

        // When
        $notification = new EmailVerificationOtpNotification($otpCode);

        // Then
        $this->assertEquals('123456', $notification->otpCode);
    }

    public function test_given_different_ot_p_codes_when_creating_notifications_then_each_stores_correct_code()
    {
        // Given
        $otpCode1 = '111111';
        $otpCode2 = '222222';

        // When
        $notification1 = new EmailVerificationOtpNotification($otpCode1);
        $notification2 = new EmailVerificationOtpNotification($otpCode2);

        // Then
        $this->assertEquals('111111', $notification1->otpCode);
        $this->assertEquals('222222', $notification2->otpCode);
    }

    // Via method tests
    public function test_given_a_user_when_determining_delivery_channels_then_it_returns_mail_channel()
    {
        // Given
        $user = User::factory()->make();
        $notification = new EmailVerificationOtpNotification('123456');

        // When
        $channels = $notification->via($user);

        // Then
        $this->assertEquals(['mail'], $channels);
    }

    // ToMail method tests
    public function test_given_a_user_and_ot_p_code_when_creating_mail_message_then_it_returns_properly_formatted_mail()
    {
        // Given
        $user = User::factory()->make(['email' => 'user@example.com']);
        $otpCode = '123456';
        $notification = new EmailVerificationOtpNotification($otpCode);

        // When
        $mailMessage = $notification->toMail($user);

        // Then
        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Verify Your Email Address', $mailMessage->subject);
        $this->assertEquals('Hello!', $mailMessage->greeting);
        $this->assertEquals('Thank you for joining us!', $mailMessage->salutation);
    }

    public function test_given_an_ot_p_code_when_creating_mail_message_then_it_includes_the_code_in_the_content()
    {
        // Given
        $user = User::factory()->make();
        $otpCode = '987654';
        $notification = new EmailVerificationOtpNotification($otpCode);

        // When
        $mailMessage = $notification->toMail($user);

        // Then
        $lineFound = false;
        foreach ($mailMessage->introLines as $line) {
            if (str_contains($line, '987654')) {
                $lineFound = true;
                break;
            }
        }
        $this->assertTrue($lineFound);
    }

    public function test_given_different_ot_p_codes_when_creating_mail_messages_then_each_includes_correct_code()
    {
        // Given
        $user = User::factory()->make();
        $notification1 = new EmailVerificationOtpNotification('111111');
        $notification2 = new EmailVerificationOtpNotification('222222');

        // When
        $mailMessage1 = $notification1->toMail($user);
        $mailMessage2 = $notification2->toMail($user);

        // Then
        $code1Found = false;
        $code2Found = false;

        foreach ($mailMessage1->introLines as $line) {
            if (str_contains($line, '111111')) {
                $code1Found = true;
                break;
            }
        }

        foreach ($mailMessage2->introLines as $line) {
            if (str_contains($line, '222222')) {
                $code2Found = true;
                break;
            }
        }

        $this->assertTrue($code1Found);
        $this->assertTrue($code2Found);
    }

    public function test_given_a_mail_message_when_checking_content_then_it_contains_verification_instructions()
    {
        // Given
        $user = User::factory()->make();
        $notification = new EmailVerificationOtpNotification('123456');

        // When
        $mailMessage = $notification->toMail($user);

        // Then
        $content = implode(' ', $mailMessage->introLines);
        $this->assertStringContainsString('verify your email address', $content);
        $this->assertStringContainsString('verification code', $content);
        $this->assertStringContainsString('expire in 10 minutes', $content);
        $this->assertStringContainsString('did not create an account', $content);
    }

    public function test_given_a_mail_message_when_checking_structure_then_it_has_proper_sections()
    {
        // Given
        $user = User::factory()->make();
        $notification = new EmailVerificationOtpNotification('123456');

        // When
        $mailMessage = $notification->toMail($user);

        // Then
        $this->assertNotEmpty($mailMessage->introLines);
        $this->assertNotEmpty($mailMessage->greeting);
        $this->assertNotEmpty($mailMessage->salutation);
        $this->assertGreaterThan(3, count($mailMessage->introLines));
    }

    // ToArray method tests
    public function test_given_a_user_when_converting_to_array_then_it_returns_empty_array()
    {
        // Given
        $user = User::factory()->make();
        $notification = new EmailVerificationOtpNotification('123456');

        // When
        $array = $notification->toArray($user);

        // Then
        $this->assertEquals([], $array);
    }

    // ShouldQueue interface tests
    public function test_given_notification_when_checking_if_it_should_be_queued_then_it_implements_should_queue()
    {
        // Given
        $notification = new EmailVerificationOtpNotification('123456');

        // Then
        $this->assertInstanceOf(ShouldQueue::class, $notification);
    }

    public function test_given_notification_when_checking_queueable_traits_then_it_uses_queueable_trait()
    {
        // Given
        $notification = new EmailVerificationOtpNotification('123456');

        // Then
        $traits = class_uses_recursive(get_class($notification));
        $this->assertArrayHasKey(\Illuminate\Bus\Queueable::class, $traits);
    }
}
