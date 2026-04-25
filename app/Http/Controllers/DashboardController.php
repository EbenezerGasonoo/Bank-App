<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('accounts.cards');
        $accounts = $user->accounts;
        $primaryAccount = $accounts->first();

        $recentTransactions = collect();
        if ($primaryAccount) {
            $recentTransactions = Transaction::where('sender_account_id', $primaryAccount->id)
                ->orWhere('receiver_account_id', $primaryAccount->id)
                ->with(['senderAccount.user', 'receiverAccount.user'])
                ->latest()
                ->take(10)
                ->get();
        }

        // Monthly spending for chart (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $spent = 0;
            if ($primaryAccount) {
                $spent = Transaction::where('sender_account_id', $primaryAccount->id)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->where('status', 'completed')
                    ->sum('amount');
            }
            $monthlyData[] = [
                'month' => $month->format('M Y'),
                'amount' => (float) $spent,
            ];
        }

        return view('dashboard', compact('user', 'accounts', 'primaryAccount', 'recentTransactions', 'monthlyData'));
    }
}
