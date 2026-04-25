<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WireCodesIssuedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $pin,
        public string $taxCode,
        public string $imfCode,
        public string $cotCode
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Wire Transfer Authentication Codes')
            ->markdown('emails.wire-codes-issued', [
                'user' => $notifiable,
                'pin' => $this->pin,
                'taxCode' => $this->taxCode,
                'imfCode' => $this->imfCode,
                'cotCode' => $this->cotCode,
            ]);
    }
}

