<?php

namespace App\Notifications;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountCreditedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Transaction $transaction,
        public Account $account
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Credit Alert: Funds Added to Your Account')
            ->markdown('emails.account-credited', [
                'user' => $notifiable,
                'transaction' => $this->transaction,
                'account' => $this->account,
            ]);
    }
}

