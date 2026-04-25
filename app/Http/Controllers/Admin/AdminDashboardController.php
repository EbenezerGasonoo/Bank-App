<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AdminActionApproval;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WireCodeRequest;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $pendingKyc = User::where('kyc_status', 'pending')->count();
        $totalAccounts = Account::count();
        $totalTransactions = Transaction::count();
        $totalDeposited = Transaction::where('type', 'deposit')->where('status', 'completed')->sum('amount');
        $pendingApprovals = AdminActionApproval::where('status', 'pending')->count();
        $pendingWireCodeRequests = WireCodeRequest::where('status', 'pending')->count();

        $recentUsers = User::where('role', 'user')->latest()->take(5)->get();
        $recentTransactions = Transaction::with(['senderAccount.user', 'receiverAccount.user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'pendingKyc', 'totalAccounts',
            'totalTransactions', 'totalDeposited', 'recentUsers', 'recentTransactions', 'pendingApprovals', 'pendingWireCodeRequests'
        ));
    }
}
