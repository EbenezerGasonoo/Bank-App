<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeMessageNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Poise Commerce Bank')
            ->greeting('Welcome ' . $notifiable->name . ',')
            ->line('Your account has been created successfully.')
            ->line('Please verify your email address to activate secure banking features.')
            ->action('Verify Email', url('/email/verify'))
            ->line('We are glad to have you with us.');
    }
}

