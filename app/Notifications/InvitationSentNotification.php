<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationSentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Invitation $notifiable): MailMessage
    {
        $invitationUrl = url()->temporarySignedRoute('invitation.handle', now()->addWeek(), [
            'token' => $notifiable->accept_token,
        ]);

        return (new MailMessage)
            ->subject(__('mail.subject.invited'))
            ->markdown('mail.invitation.sent', [
                'invitation' => $notifiable,
                'invitationUrl' => $invitationUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(Invitation $notifiable): array
    {
        return $notifiable->toArray();
    }
}
