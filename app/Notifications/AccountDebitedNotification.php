<?php

namespace App\Notifications;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDebitedNotification extends Notification
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
            ->subject('Debit Alert: Funds Removed from Your Account')
            ->markdown('emails.account-debited', [
                'user' => $notifiable,
                'transaction' => $this->transaction,
                'account' => $this->account,
            ]);
    }
}

