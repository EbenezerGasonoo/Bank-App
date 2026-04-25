<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Fdr;
use App\Services\TransactionEngine;
use Illuminate\Http\Request;

class FdrController extends Controller
{
    private const RATE_BY_TERM = [
        3 => 4.80,
        6 => 5.20,
        12 => 5.80,
        24 => 6.40,
    ];

    public function __construct(private TransactionEngine $engine)
    {
    }

    public function index(Request $request)
    {
        abort_if($request->user()?->isAdmin(), 403);
        $user = $request->user();
        $accounts = $user->accounts()->where('status', 'active')->get();
        $fdrs = $user->fdrs()->with('account')->latest()->paginate(10);

        return view('fdrs.index', compact('accounts', 'fdrs'));
    }

    public function store(Request $request)
    {
        abort_if($request->user()?->isAdmin(), 403);
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'principal' => 'required|numeric|min:500',
            'term_months' => 'required|in:3,6,12,24',
            'payout_mode' => 'required|in:maturity,monthly',
            'notes' => 'nullable|string|max:150',
        ]);

        $user = $request->user();
        $account = Account::where('id', $request->account_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $principal = (float) $request->principal;
        $termMonths = (int) $request->term_months;
        $annualRate = self::RATE_BY_TERM[$termMonths] ?? 5.00;
        $interest = round($principal * ($annualRate / 100) * ($termMonths / 12), 2);
        $maturityAmount = round($principal + $interest, 2);

        try {
            $this->engine->withdraw(
                $account,
                $principal,
                'FDR placement (' . $termMonths . ' months)'
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['principal' => $e->getMessage()]);
        }

        Fdr::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'principal' => $principal,
            'annual_rate' => $annualRate,
            'term_months' => $termMonths,
            'payout_mode' => $request->payout_mode,
            'expected_interest' => $interest,
            'maturity_amount' => $maturityAmount,
            'status' => 'active',
            'starts_at' => now(),
            'matures_at' => now()->copy()->addMonths($termMonths),
            'notes' => $request->notes,
        ]);

        return redirect()->route('fdrs.index')
            ->with('success', 'FDR opened successfully. Funds have been placed from your account.');
    }
}

