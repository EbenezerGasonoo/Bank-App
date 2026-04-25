<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatementController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()?->isAdmin(), 403);
        $user = $request->user();
        $accountIds = $user->accounts()->pluck('id');

        $query = Transaction::query()
            ->where(function ($q) use ($accountIds) {
                $q->whereIn('sender_account_id', $accountIds)
                    ->orWhereIn('receiver_account_id', $accountIds);
            })
            ->with(['senderAccount.user', 'receiverAccount.user']);

        [$fromDate, $toDate] = $this->extractPeriodDates($request);

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('from_amount')) {
            $query->where('amount', '>=', (float) $request->input('from_amount'));
        }
        if ($request->filled('to_amount')) {
            $query->where('amount', '<=', (float) $request->input('to_amount'));
        }

        $transactions = (clone $query)->latest()->paginate(20)->withQueryString();
        $allFiltered = (clone $query)->latest()->get();

        $creditTotal = 0.0;
        $debitTotal = 0.0;
        foreach ($allFiltered as $tx) {
            if ($this->isCredit($tx, $accountIds->all())) {
                $creditTotal += (float) $tx->amount;
            } else {
                $debitTotal += (float) $tx->amount;
            }
        }

        return view('statement.index', [
            'transactions' => $transactions,
            'creditTotal' => $creditTotal,
            'debitTotal' => $debitTotal,
            'netTotal' => $creditTotal - $debitTotal,
            'accountIds' => $accountIds,
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        abort_if($request->user()?->isAdmin(), 403);
        $user = $request->user();
        $accountIds = $user->accounts()->pluck('id');

        $query = Transaction::query()
            ->where(function ($q) use ($accountIds) {
                $q->whereIn('sender_account_id', $accountIds)
                    ->orWhereIn('receiver_account_id', $accountIds);
            })
            ->with(['senderAccount.user', 'receiverAccount.user'])
            ->latest();

        [$fromDate, $toDate] = $this->extractPeriodDates($request);

        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('from_amount')) {
            $query->where('amount', '>=', (float) $request->input('from_amount'));
        }
        if ($request->filled('to_amount')) {
            $query->where('amount', '<=', (float) $request->input('to_amount'));
        }

        $rows = $query->get();

        return response()->streamDownload(function () use ($rows, $accountIds) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Reference', 'Type', 'Direction', 'From', 'To', 'Amount', 'Status', 'Description']);

            foreach ($rows as $tx) {
                $isCredit = $this->isCredit($tx, $accountIds->all());
                fputcsv($handle, [
                    $tx->created_at->format('Y-m-d H:i:s'),
                    $tx->reference,
                    $tx->type,
                    $isCredit ? 'Credit' : 'Debit',
                    optional(optional($tx->senderAccount)->user)->name ?? 'N/A',
                    optional(optional($tx->receiverAccount)->user)->name ?? 'N/A',
                    number_format((float) $tx->amount, 2, '.', ''),
                    $tx->status,
                    $tx->description ?? '',
                ]);
            }

            fclose($handle);
        }, 'statement-' . now()->format('Ymd-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function isCredit(Transaction $tx, array $accountIds): bool
    {
        if ($tx->type === 'deposit') {
            return true;
        }

        return in_array((int) $tx->receiver_account_id, $accountIds, true);
    }

    private function extractPeriodDates(Request $request): array
    {
        if ($request->filled('period')) {
            $period = (string) $request->input('period');
            $parts = preg_split('/\s*-\s*/', $period) ?: [];
            $fromDate = $parts[0] ?? null;
            $toDate = $parts[1] ?? null;

            return [$fromDate ?: null, $toDate ?: null];
        }

        $fromDate = $request->filled('from_date') ? $request->string('from_date')->toString() : null;
        $toDate = $request->filled('to_date') ? $request->string('to_date')->toString() : null;

        return [$fromDate, $toDate];
    }
}
