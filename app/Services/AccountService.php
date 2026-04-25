<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;

class AccountService
{
    public function createAccount(User $user, string $type = 'checking'): Account
    {
        $accountNumber = $this->generateUniqueAccountNumber();

        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => $accountNumber,
            'type' => $type,
            'balance' => 0.00,
            'status' => 'active',
            'currency' => 'GBP',
        ]);

        // Auto-generate a virtual card for the account
        $this->issueCard($account, $user->name);

        return $account;
    }

    public function generateUniqueAccountNumber(): string
    {
        do {
            $number = '20' . str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $number)->exists());

        return $number;
    }

    public function issueCard(Account $account, string $cardholderName): Card
    {
        $rawNumber = '4' . str_pad(random_int(0, 999999999999999), 15, '0', STR_PAD_LEFT);
        $last4 = substr($rawNumber, -4);
        $masked = '**** **** **** ' . $last4;
        $expiry = now()->addYears(4)->format('m/Y');
        $cvv = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);

        return Card::create([
            'account_id' => $account->id,
            'card_number_masked' => $masked,
            'card_number_encrypted' => Crypt::encryptString($rawNumber),
            'expiration' => $expiry,
            'cvv_encrypted' => Crypt::encryptString($cvv),
            'cardholder_name' => strtoupper($cardholderName),
            'is_frozen' => false,
        ]);
    }
}
