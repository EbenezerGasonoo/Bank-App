<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $otpCode,
        public int $expiryMinutes = 10
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Login OTP Code')
            ->markdown('emails.login-otp', [
                'user' => $notifiable,
                'otpCode' => $this->otpCode,
                'expiryMinutes' => $this->expiryMinutes,
            ]);
    }
}

