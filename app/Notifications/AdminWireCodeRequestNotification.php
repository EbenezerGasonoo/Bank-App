<?php

namespace App\Notifications;

use App\Models\WireCodeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminWireCodeRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public WireCodeRequest $requestModel)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Wire Code Request')
            ->line('A customer has requested transfer authentication codes.')
            ->line('Customer: ' . $this->requestModel->user->name . ' (' . $this->requestModel->user->email . ')')
            ->action('Review Request', route('admin.wire-requests.index'))
            ->line('Please generate and issue codes from the admin panel.');
    }
}

