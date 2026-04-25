<?php

namespace App\Http\Controllers;

use App\Models\DpsRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DpsController extends Controller
{
    public function index(Request $request): View
    {
        abort_if($request->user()?->isAdmin(), 403);
        $user = $request->user();

        $dpsRequests = DpsRequest::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $summary = [
            'total_monthly' => (float) DpsRequest::where('user_id', $user->id)->sum('monthly_amount'),
            'pending_count' => DpsRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            'active_count' => DpsRequest::where('user_id', $user->id)->where('status', 'active')->count(),
        ];

        return view('dps.index', compact('dpsRequests', 'summary'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()?->isAdmin(), 403);
        $validated = $request->validate([
            'monthly_amount' => ['required', 'numeric', 'min:10', 'max:1000000'],
            'tenure_months' => ['required', 'integer', 'min:6', 'max:240'],
            'plan_name' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1200'],
        ]);

        DpsRequest::create([
            'user_id' => $request->user()->id,
            'monthly_amount' => (float) $validated['monthly_amount'],
            'tenure_months' => (int) $validated['tenure_months'],
            'plan_name' => $validated['plan_name'],
            'notes' => $validated['notes'] ?? null,
            'interest_rate' => 6.50,
            'status' => 'pending',
        ]);

        return redirect()->route('dps.index')->with('success', 'DPS plan request submitted successfully.');
    }
}
