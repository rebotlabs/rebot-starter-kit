<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationSent extends Notification
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
        $acceptUrl = url()->temporarySignedRoute('invitation.accept', now()->addWeek(), [
            'token' => $notifiable->accept_token,
        ]);

        $rejectUrl = url()->temporarySignedRoute('invitation.reject', now()->addWeek(), [
            'token' => $notifiable->reject_token,
        ]);

        return (new MailMessage)
            ->subject('You have been invited!')
            ->markdown('mail.invitation.sent', [
                'invitation' => $notifiable,
                'acceptUrl' => $acceptUrl,
                'rejectUrl' => $rejectUrl,
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
