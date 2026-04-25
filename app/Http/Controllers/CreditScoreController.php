<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class CreditScoreController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('dashboard')
                ->withErrors(['credit_score' => 'Credit score view is available for customer accounts only.']);
        }

        $accountIds = $user->accounts()->pluck('id');
        $totalTx = 0;
        $completedTx = 0;
        $riskTx = 0;

        if ($accountIds->isNotEmpty()) {
            $baseQuery = Transaction::where(function ($q) use ($accountIds) {
                $q->whereIn('sender_account_id', $accountIds)
                    ->orWhereIn('receiver_account_id', $accountIds);
            });

            $totalTx = (clone $baseQuery)->count();
            $completedTx = (clone $baseQuery)->where('status', 'completed')->count();
            $riskTx = (clone $baseQuery)->whereIn('status', ['failed', 'flagged'])->count();
        }

        $score = 520;
        if ($user->hasVerifiedEmail()) {
            $score += 40;
        }
        if ($user->kyc_status === 'approved') {
            $score += 60;
        }
        if ($user->phone) {
            $score += 20;
        }
        if ($user->account_status === 'active') {
            $score += 30;
        }
        if ($totalTx > 0) {
            $score += min(90, (int) round(($completedTx / $totalTx) * 90));
        }
        $score -= min(90, $riskTx * 10);
        $score = max(300, min(850, $score));

        $band = match (true) {
            $score >= 780 => 'Excellent',
            $score >= 720 => 'Very Good',
            $score >= 660 => 'Good',
            $score >= 600 => 'Fair',
            default => 'Needs Improvement',
        };

        $factors = [
            ['label' => 'Email verification', 'state' => $user->hasVerifiedEmail() ? 'positive' : 'negative'],
            ['label' => 'KYC status', 'state' => $user->kyc_status === 'approved' ? 'positive' : 'negative'],
            ['label' => 'Account status', 'state' => $user->account_status === 'active' ? 'positive' : 'negative'],
            ['label' => 'Transaction quality', 'state' => $riskTx === 0 ? 'positive' : 'negative'],
        ];

        return view('credit-score.show', compact('score', 'band', 'factors', 'totalTx', 'completedTx', 'riskTx'));
    }
}

