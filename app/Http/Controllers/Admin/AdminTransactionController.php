<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class AdminTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['senderAccount.user', 'receiverAccount.user'])->latest();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $status = $request->status === 'rejected' ? 'failed' : $request->status;
            $query->where('status', $status);
        }

        if ($request->search) {
            $query->where('reference', 'like', "%{$request->search}%");
        }

        if ($request->transfer_filter) {
            $query->where('type', 'transfer');
            $this->applyTransferFilter($query, $request->transfer_filter);
        }

        if ($request->withdrawal_filter) {
            $query->where('type', 'withdrawal');
            $this->applyWithdrawalFilter($query, $request->withdrawal_filter);
        }

        if ($request->deposit_filter) {
            $query->where('type', 'deposit');
            $this->applyDepositFilter($query, $request->deposit_filter);
        }

        $transactions = $query->paginate(25)->withQueryString();

        $txCounts = [
            'pending_transfers' => $this->countTransfers('pending'),
            'rejected_transfers' => $this->countTransfers('failed'),
            'own_bank_transfers' => $this->countOwnBankTransfers(),
            'other_bank_transfers' => $this->countOtherBankTransfers(),
            'wire_transfers' => $this->countWireTransfers(),
            'all_transfers' => $this->countTransfers(),
            'pending_withdrawals' => Transaction::where('type', 'withdrawal')->where('status', 'pending')->count(),
            'approved_withdrawals' => Transaction::where('type', 'withdrawal')->where('status', 'completed')->count(),
            'rejected_withdrawals' => Transaction::where('type', 'withdrawal')->where('status', 'failed')->count(),
            'all_withdrawals' => Transaction::where('type', 'withdrawal')->count(),
            'pending_deposits' => Transaction::where('type', 'deposit')->where('status', 'pending')->count(),
            'approved_deposits' => Transaction::where('type', 'deposit')->where('status', 'completed')->count(),
            'successful_deposits' => Transaction::where('type', 'deposit')->where('status', 'completed')->count(),
            'rejected_deposits' => Transaction::where('type', 'deposit')->where('status', 'failed')->count(),
            'initiated_deposits' => Transaction::where('type', 'deposit')->whereIn('status', ['pending', 'completed', 'failed', 'flagged'])->count(),
            'all_deposits' => Transaction::where('type', 'deposit')->count(),
        ];

        return view('admin.transactions.index', compact('transactions', 'txCounts'));
    }

    public function flag(Transaction $transaction)
    {
        $transaction->update(['status' => 'flagged']);
        return back()->with('success', 'Transaction flagged as suspicious.');
    }

    private function applyTransferFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'pending' => $query->where('status', 'pending'),
            'rejected' => $query->where('status', 'failed'),
            'own_bank' => $query->whereNotNull('sender_account_id')
                ->whereNotNull('receiver_account_id')
                ->whereColumn('sender_account_id', 'receiver_account_id'),
            'other_bank' => $query->whereNotNull('sender_account_id')
                ->whereNotNull('receiver_account_id')
                ->whereColumn('sender_account_id', '!=', 'receiver_account_id'),
            'wire' => $query->where(function (Builder $builder) {
                $builder->where('reference', 'like', 'WIRE-%')
                    ->orWhere('description', 'like', '%wire%');
            }),
            default => null,
        };
    }

    private function applyWithdrawalFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'pending' => $query->where('status', 'pending'),
            'approved' => $query->where('status', 'completed'),
            'rejected' => $query->where('status', 'failed'),
            default => null,
        };
    }

    private function applyDepositFilter(Builder $query, string $filter): void
    {
        match ($filter) {
            'pending' => $query->where('status', 'pending'),
            'approved' => $query->where('status', 'completed'),
            'successful' => $query->where('status', 'completed'),
            'rejected' => $query->where('status', 'failed'),
            'initiated' => $query->whereIn('status', ['pending', 'completed', 'failed', 'flagged']),
            default => null,
        };
    }

    private function countTransfers(?string $status = null): int
    {
        $query = Transaction::where('type', 'transfer');
        if ($status) {
            $query->where('status', $status);
        }
        return $query->count();
    }

    private function countOwnBankTransfers(): int
    {
        return Transaction::where('type', 'transfer')
            ->whereNotNull('sender_account_id')
            ->whereNotNull('receiver_account_id')
            ->whereColumn('sender_account_id', 'receiver_account_id')
            ->count();
    }

    private function countOtherBankTransfers(): int
    {
        return Transaction::where('type', 'transfer')
            ->whereNotNull('sender_account_id')
            ->whereNotNull('receiver_account_id')
            ->whereColumn('sender_account_id', '!=', 'receiver_account_id')
            ->count();
    }

    private function countWireTransfers(): int
    {
        return Transaction::where('type', 'transfer')
            ->where(function (Builder $builder) {
                $builder->where('reference', 'like', 'WIRE-%')
                    ->orWhere('description', 'like', '%wire%');
            })
            ->count();
    }
}
