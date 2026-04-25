<?php

namespace App\Services;

use App\Models\Account;
use App\Models\LedgerEntry;
use App\Models\Transaction;
use App\Notifications\AccountCreditedNotification;
use App\Notifications\AccountDebitedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionEngine
{
    /**
     * Create a deposit to an account (admin-initiated).
     */
    public function deposit(
        Account $account,
        float $amount,
        string $description = 'Deposit',
        ?string $reference = null,
        ?Carbon $postedAt = null
    ): Transaction
    {
        return DB::transaction(function () use ($account, $amount, $description, $reference, $postedAt) {
            $effectiveDate = $postedAt ?? now();
            $transaction = Transaction::create([
                'sender_account_id' => null,
                'receiver_account_id' => $account->id,
                'amount' => $amount,
                'type' => 'deposit',
                'reference' => $reference ?: $this->generateReference(),
                'status' => 'completed',
                'description' => $description,
                'created_at' => $effectiveDate,
                'updated_at' => $effectiveDate,
            ]);

            // Double-entry: receiver is debited (gains funds)
            LedgerEntry::create([
                'transaction_id' => $transaction->id,
                'debit_account_id' => $account->id,
                'credit_account_id' => $account->id,
                'amount' => $amount,
            ]);

            $account->increment('balance', $amount);
            $account->refresh();

            DB::afterCommit(function () use ($account, $transaction): void {
                $account->user?->notify(new AccountCreditedNotification($transaction, $account));
            });

            return $transaction;
        });
    }

    /**
     * Create a withdrawal from an account (admin-initiated).
     */
    public function withdraw(
        Account $account,
        float $amount,
        string $description = 'Withdrawal',
        ?string $reference = null,
        ?Carbon $postedAt = null
    ): Transaction
    {
        return DB::transaction(function () use ($account, $amount, $description, $reference, $postedAt) {
            // Lock the row to prevent race conditions
            $locked = Account::lockForUpdate()->find($account->id);
            $effectiveDate = $postedAt ?? now();

            if ($locked->balance < $amount) {
                throw new \RuntimeException('Insufficient funds.');
            }

            $transaction = Transaction::create([
                'sender_account_id' => $locked->id,
                'receiver_account_id' => null,
                'amount' => $amount,
                'type' => 'withdrawal',
                'reference' => $reference ?: $this->generateReference(),
                'status' => 'completed',
                'description' => $description,
                'created_at' => $effectiveDate,
                'updated_at' => $effectiveDate,
            ]);

            LedgerEntry::create([
                'transaction_id' => $transaction->id,
                'debit_account_id' => $locked->id,
                'credit_account_id' => $locked->id,
                'amount' => $amount,
            ]);

            $locked->decrement('balance', $amount);
            $locked->refresh();

            DB::afterCommit(function () use ($locked, $transaction): void {
                $locked->user?->notify(new AccountDebitedNotification($transaction, $locked));
            });

            return $transaction;
        });
    }

    /**
     * Transfer funds between two accounts.
     */
    public function transfer(Account $sender, Account $receiver, float $amount, string $description = 'Transfer'): Transaction
    {
        return DB::transaction(function () use ($sender, $receiver, $amount, $description) {
            $lockedSender = Account::lockForUpdate()->find($sender->id);

            if ($lockedSender->balance < $amount) {
                throw new \RuntimeException('Insufficient funds.');
            }

            if ($lockedSender->status !== 'active') {
                throw new \RuntimeException('Sender account is not active.');
            }

            $lockedReceiver = Account::lockForUpdate()->find($receiver->id);

            if ($lockedReceiver->status !== 'active') {
                throw new \RuntimeException('Receiver account is not active.');
            }

            $transaction = Transaction::create([
                'sender_account_id' => $lockedSender->id,
                'receiver_account_id' => $lockedReceiver->id,
                'amount' => $amount,
                'type' => 'transfer',
                'reference' => $this->generateReference(),
                'status' => 'completed',
                'description' => $description,
            ]);

            // Double-entry ledger
            LedgerEntry::create([
                'transaction_id' => $transaction->id,
                'debit_account_id' => $lockedReceiver->id,   // receiver gains
                'credit_account_id' => $lockedSender->id,    // sender loses
                'amount' => $amount,
            ]);

            $lockedSender->decrement('balance', $amount);
            $lockedReceiver->increment('balance', $amount);

            return $transaction;
        });
    }

    private function generateReference(): string
    {
        return 'LBT-' . strtoupper(Str::random(10));
    }
}
