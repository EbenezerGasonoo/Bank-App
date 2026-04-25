<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\LedgerEntry;
use App\Models\Announcement;
use App\Services\AccountService;
use App\Services\TransactionEngine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $accountService = app(AccountService::class);
        $engine = app(TransactionEngine::class);

        // ─── Super Admin ───────────────────────────────────────
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@poisecommercebank.com',
            'password' => Hash::make('Admin@12345'),
            'role' => 'super_admin',
            'kyc_status' => 'approved',
            'account_status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+44 20 7123 4567',
            'date_of_birth' => '1985-01-01',
            'address' => '1 Bankers Way, London, EC2V 8RT',
        ]);

        // ─── Support Staff ─────────────────────────────────────
        User::create([
            'name' => 'Sarah Support',
            'email' => 'support@poisecommercebank.com',
            'password' => Hash::make('Support@12345'),
            'role' => 'support',
            'kyc_status' => 'approved',
            'account_status' => 'active',
            'email_verified_at' => now(),
        ]);

        // ─── Demo User 1 (Approved, with account) ─────────────
        $alice = User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'password' => Hash::make('Password@123'),
            'role' => 'user',
            'kyc_status' => 'approved',
            'account_status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+44 7700 900001',
            'date_of_birth' => '1992-05-14',
            'address' => '42 Baker Street, London, W1U 7EP',
        ]);
        $aliceAccount = $accountService->createAccount($alice, 'checking');
        $engine->deposit($aliceAccount, 5000.00, 'Welcome bonus');
        $engine->deposit($aliceAccount, 2500.00, 'Initial deposit');

        // ─── Demo User 2 (Approved, Savings) ──────────────────
        $bob = User::create([
            'name' => 'Bob Williams',
            'email' => 'bob@example.com',
            'password' => Hash::make('Password@123'),
            'role' => 'user',
            'kyc_status' => 'approved',
            'account_status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+44 7700 900002',
            'date_of_birth' => '1988-09-22',
            'address' => '15 Cannon Street, London, EC4N 5AD',
        ]);
        $bobAccount = $accountService->createAccount($bob, 'savings');
        $engine->deposit($bobAccount, 10000.00, 'Transfer from savings');

        // ─── Demo Transfer ─────────────────────────────────────
        $engine->transfer($aliceAccount, $bobAccount, 250.00, 'Payment for invoice #123');
        $engine->transfer($bobAccount, $aliceAccount, 99.50, 'Dinner split');

        // ─── Pending KYC User ─────────────────────────────────
        User::create([
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
            'password' => Hash::make('Password@123'),
            'role' => 'user',
            'kyc_status' => 'pending',
            'account_status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+44 7700 900003',
            'date_of_birth' => '1995-03-10',
            'address' => '7 Lombard Street, London, EC3V 9AS',
        ]);

        // ─── Announcements ────────────────────────────────────
        Announcement::create([
            'user_id' => $admin->id,
            'title' => 'New Feature: Instant Transfers Now Live',
            'body' => 'We are excited to announce that instant transfers are now available 24/7. Move money to any Poise Commerce Bank account in seconds.',
            'type' => 'info',
            'is_published' => true,
        ]);
        Announcement::create([
            'user_id' => $admin->id,
            'title' => 'Scheduled Maintenance Notice',
            'body' => 'Our systems will undergo scheduled maintenance on April 5th, 2026 from 2:00 AM to 4:00 AM GMT. Services may be briefly unavailable.',
            'type' => 'warning',
            'is_published' => true,
        ]);
        Announcement::create([
            'user_id' => $admin->id,
            'title' => 'Fraud Alert: Phishing Attempts Detected',
            'body' => 'We have detected phishing emails targeting our customers. Poise Commerce Bank will never ask for your password via email. Please report suspicious messages.',
            'type' => 'alert',
            'is_published' => true,
        ]);
    }
}
