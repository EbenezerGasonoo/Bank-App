<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()?->isAdmin(), 403);
        $user = $request->user();

        $loanRequests = LoanRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $summary = [
            'total_requested' => (float) LoanRequest::where('user_id', $user->id)->sum('amount'),
            'pending_count' => LoanRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved_count' => LoanRequest::where('user_id', $user->id)->where('status', 'approved')->count(),
        ];

        return view('loan.index', compact('loanRequests', 'summary'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()?->isAdmin(), 403);
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:500', 'max:10000000'],
            'duration_months' => ['required', 'integer', 'min:3', 'max:120'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1200'],
        ]);

        LoanRequest::create([
            'user_id' => $request->user()->id,
            'amount' => (float) $validated['amount'],
            'duration_months' => (int) $validated['duration_months'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null,
            'interest_rate' => 8.50,
            'status' => 'pending',
        ]);

        return redirect()->route('loan.index')->with('success', 'Loan request submitted successfully.');
    }
}
