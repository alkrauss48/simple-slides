<?php

namespace App\Notifications;

use App\Http\Controllers\InvitationController;
use App\Models\PresentationUser;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PresentationUserCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public PresentationUser $presentationUser
    ) {
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
    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = InvitationController::generateAcceptUrl($this->presentationUser);

        return (new MailMessage)
            ->subject('You\'ve been invited to collaborate on "'.$this->presentationUser->presentation->title.'"')
            ->greeting('Hello!')
            ->line($this->presentationUser->presentation->user->name.' has invited you to collaborate on their presentation "'.$this->presentationUser->presentation->title.'".')
            ->line('Click the button below to accept this invitation and start collaborating.')
            ->action('Accept Invitation', $acceptUrl)
            ->line('This invitation will expire in 7 days.')
            ->line('If you\'re unable to click the button, copy and paste the following URL into your browser:')
            ->line($acceptUrl)
            ->salutation('Best regards, The Simple Slides Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'presentation_id' => $this->presentationUser->presentation_id,
            'presentation_title' => $this->presentationUser->presentation->title,
            'invited_by' => $this->presentationUser->presentation->user->name,
        ];
    }
}
