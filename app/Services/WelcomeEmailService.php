<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\WelcomeMessageNotification;

class WelcomeEmailService
{
    public function sendIfEligible(User $user, ?User $actor = null, ?string $ipAddress = null): bool
    {
        if (!$user->hasVerifiedEmail() || !$user->accounts()->exists()) {
            return false;
        }

        $alreadySent = AuditLog::where('action', 'user.welcome_email.sent')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->exists();

        if ($alreadySent) {
            return false;
        }

        $user->notify(new WelcomeMessageNotification());

        AuditLog::create([
            'user_id' => $actor?->id ?? $user->id,
            'action' => 'user.welcome_email.sent',
            'model_type' => 'User',
            'model_id' => $user->id,
            'changes' => [
                'email' => $user->email,
                'account_number' => $user->primaryAccount()?->account_number,
            ],
            'ip_address' => $ipAddress,
        ]);

        return true;
    }
}
