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
        $accountNumber = $notifiable->primaryAccount()?->account_number ?? 'Not assigned yet';

        return (new MailMessage)
            ->subject('Welcome to Poise Commerce Bank')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Welcome to Poise Commerce Bank Private Banking. Your email address has been verified successfully.')
            ->line('Your account profile is now active. For your records, your details are as follows:')
            ->line('Full Name: ' . $notifiable->name)
            ->line('Account Number: ' . $accountNumber)
            ->line('Thank you for placing your trust in us. We look forward to supporting your banking needs with the highest standard of service and discretion.');
    }
}

