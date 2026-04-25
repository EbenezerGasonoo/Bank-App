<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AdminActionApproval;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WireCodeRequest;
use App\Notifications\AdminWireCodeRequestNotification;
use App\Services\TransactionEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function __construct(private TransactionEngine $engine) {}

    public function index(Request $request)
    {
        $this->ensureCustomer($request);
        $user = Auth::user();
        $accounts = $user->accounts()->where('status', 'active')->get();
        $accountIds = $user->accounts()->pluck('id');

        $query = Transaction::query()
            ->where(function ($q) use ($accountIds) {
                $q->whereIn('sender_account_id', $accountIds)
                    ->orWhereIn('receiver_account_id', $accountIds);
            })
            ->with(['senderAccount.user', 'receiverAccount.user']);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $transactions = (clone $query)->latest()->paginate(20)->withQueryString();
        $filteredTransactions = (clone $query)->get();

        $creditTotal = 0.0;
        $debitTotal = 0.0;
        foreach ($filteredTransactions as $tx) {
            $isCredit = $tx->type === 'deposit' || $accountIds->contains($tx->receiver_account_id);
            if ($isCredit) {
                $creditTotal += (float) $tx->amount;
            } else {
                $debitTotal += (float) $tx->amount;
            }
        }

        $txSummary = [
            'count' => $filteredTransactions->count(),
            'credit_total' => $creditTotal,
            'debit_total' => $debitTotal,
            'net_total' => $creditTotal - $debitTotal,
        ];

        return view('transactions.index', compact('transactions', 'txSummary', 'accountIds', 'accounts'));
    }

    public function showTransfer()
    {
        $this->ensureCustomer(request());
        $accounts = Auth::user()->accounts()->where('status', 'active')->get();
        return view('transactions.transfer', compact('accounts'));
    }

    public function showCryptoWithdrawal()
    {
        $this->ensureCustomer(request());
        return view('transactions.crypto-withdrawal');
    }

    public function transfer(Request $request)
    {
        $this->ensureCustomer($request);
        $user = Auth::user();
        $request->validate([
            'transfer_mode' => 'required|in:local,international',
            'sender_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $sender = Account::where('id', $request->sender_account_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($request->transfer_mode === 'international') {
            $request->validate([
                'beneficiary_name' => 'required|string|max:120',
                'beneficiary_bank' => 'required|string|max:120',
                'beneficiary_account_number' => 'required|string|max:64',
                'swift_code' => 'required|string|max:32',
                'beneficiary_country' => 'required|string|max:80',
                'pin' => 'required|string|min:4|max:12',
                'tax_code' => 'required|string|min:3|max:32',
                'imf_code' => 'required|string|min:3|max:32',
                'cot_code' => 'required|string|min:3|max:32',
            ]);

            if (!$user->hasWireAuthCodesConfigured()) {
                return back()->withErrors([
                    'pin' => 'Wire authentication codes are not yet issued. Request codes first.',
                ])->withInput();
            }

            if (!$user->validateWireAuthCodes(
                (string) $request->pin,
                (string) $request->tax_code,
                (string) $request->imf_code,
                (string) $request->cot_code
            )) {
                return back()->withErrors([
                    'pin' => 'One or more transfer authentication codes are invalid.',
                ])->withInput();
            }

            AdminActionApproval::create([
                'action' => 'customer_transfer_request',
                'target_type' => Account::class,
                'target_id' => $sender->id,
                'payload' => [
                    'amount' => (float) $request->amount,
                    'description' => $request->description ?? 'International transfer',
                    'beneficiary_name' => $request->beneficiary_name,
                    'beneficiary_bank' => $request->beneficiary_bank,
                    'beneficiary_account_number' => $request->beneficiary_account_number,
                    'swift_code' => $request->swift_code,
                    'beneficiary_country' => $request->beneficiary_country,
                ],
                'reason' => 'Customer international transfer request',
                'requested_by' => Auth::id(),
                'status' => 'pending',
            ]);

            return redirect()->route('transactions.index')
                ->with('success', 'International transfer submitted for admin approval.');
        }

        $request->validate([
            'receiver_account_number' => 'required|string',
        ]);

        $receiver = Account::where('account_number', $request->receiver_account_number)->first();

        if (!$receiver) {
            return back()->withErrors(['receiver_account_number' => 'Account not found.']);
        }

        if ($sender->id === $receiver->id) {
            return back()->withErrors(['receiver_account_number' => 'Cannot transfer to the same account.']);
        }

        $otp = (string) random_int(100000, 999999);
        $otpPayload = [
            'sender_account_id' => $sender->id,
            'sender_account_number' => $sender->account_number,
            'receiver_account_number' => $receiver->account_number,
            'receiver_account_name' => $receiver->user->name,
            'amount' => (float) $request->amount,
            'description' => $request->description ?? 'Transfer',
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10)->toIso8601String(),
        ];

        $request->session()->put('pending_transfer_otp', $otpPayload);
        $request->session()->put('pending_transfer_otp_notice', 'A verification code was sent to your email address.');

        Mail::raw(
            "Your Poise Commerce Bank transfer code is {$otp}. It expires in 10 minutes.",
            fn ($message) => $message
                ->to((string) Auth::user()->email)
                ->subject('Transfer verification code')
        );

        return redirect()->route('transactions.verifyTransfer');
    }

    public function showVerifyTransfer(Request $request)
    {
        $this->ensureCustomer($request);
        $pendingTransfer = $request->session()->get('pending_transfer_otp');

        if (!$pendingTransfer) {
            return redirect()->route('transactions.transfer')
                ->withErrors(['otp' => 'No transfer is awaiting verification.']);
        }

        return view('transactions.verify-transfer', [
            'pendingTransfer' => $pendingTransfer,
            'notice' => $request->session()->pull('pending_transfer_otp_notice'),
        ]);
    }

    public function verifyTransfer(Request $request): RedirectResponse
    {
        $this->ensureCustomer($request);
        $request->validate([
            'otp' => 'required|digits:6',
            'pin' => 'required|string|min:4|max:12',
            'tax_code' => 'required|string|min:3|max:32',
            'imf_code' => 'required|string|min:3|max:32',
            'cot_code' => 'required|string|min:3|max:32',
        ]);

        $pendingTransfer = $request->session()->get('pending_transfer_otp');

        if (!$pendingTransfer) {
            return redirect()->route('transactions.transfer')
                ->withErrors(['otp' => 'No transfer is awaiting verification.']);
        }

        if (now()->greaterThan($pendingTransfer['expires_at'])) {
            $request->session()->forget('pending_transfer_otp');

            return redirect()->route('transactions.transfer')
                ->withErrors(['otp' => 'This code has expired. Please start the transfer again.']);
        }

        if (!Hash::check($request->otp, $pendingTransfer['otp_hash'])) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        $user = Auth::user();
        if (!$user->hasWireAuthCodesConfigured()) {
            return back()->withErrors([
                'pin' => 'Wire authentication codes are not yet issued. Request codes first.',
            ]);
        }

        if (!$user->validateWireAuthCodes(
            (string) $request->pin,
            (string) $request->tax_code,
            (string) $request->imf_code,
            (string) $request->cot_code
        )) {
            return back()->withErrors([
                'pin' => 'One or more transfer authentication codes are invalid.',
            ]);
        }

        $sender = Account::where('id', $pendingTransfer['sender_account_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $receiver = Account::where('account_number', $pendingTransfer['receiver_account_number'])->first();

        if (!$receiver || $sender->id === $receiver->id) {
            $request->session()->forget('pending_transfer_otp');

            return redirect()->route('transactions.transfer')
                ->withErrors(['receiver_account_number' => 'The destination account is no longer valid.']);
        }

        try {
            $this->engine->transfer(
                $sender,
                $receiver,
                (float) $pendingTransfer['amount'],
                $pendingTransfer['description']
            );
            $request->session()->forget('pending_transfer_otp');

            return redirect()->route('dashboard')
                ->with('success', 'Transfer of £' . number_format($pendingTransfer['amount'], 2) . ' completed successfully!');
        } catch (\RuntimeException $e) {
            return redirect()->route('transactions.transfer')->withErrors(['amount' => $e->getMessage()]);
        }
    }

    public function requestWireCodes(Request $request): RedirectResponse
    {
        $this->ensureCustomer($request);
        $user = $request->user();

        if ($user->isAdmin()) {
            return back()->withErrors(['wire_codes' => 'Admins do not require wire transfer codes.']);
        }

        $alreadyPending = WireCodeRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()->withErrors(['wire_codes' => 'You already have a pending email code request.']);
        }

        $wireRequest = WireCodeRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        AdminActionApproval::create([
            'action' => 'customer_wire_code_request',
            'target_type' => User::class,
            'target_id' => $user->id,
            'payload' => [
                'wire_request_id' => $wireRequest->id,
                'email' => $user->email,
            ],
            'reason' => 'Customer requested wire transfer authentication codes',
            'requested_by' => $user->id,
            'status' => 'pending',
        ]);

        User::whereIn('role', ['super_admin', 'admin', 'support', 'auditor'])
            ->get()
            ->each(fn (User $admin) => $admin->notify(new AdminWireCodeRequestNotification($wireRequest)));

        return back()->with('success', 'Email code request sent to the bank. You will receive it once approved.');
    }

    public function requestDeposit(Request $request): RedirectResponse
    {
        $this->ensureCustomer($request);

        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:10'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $account = Account::query()
            ->where('id', $validated['account_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        AdminActionApproval::create([
            'action' => 'customer_deposit_request',
            'target_type' => Account::class,
            'target_id' => $account->id,
            'payload' => [
                'amount' => (float) $validated['amount'],
                'description' => $validated['description'] ?? 'Customer deposit request',
            ],
            'reason' => 'Customer deposit request',
            'requested_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Deposit request submitted for admin approval.');
    }

    private function ensureCustomer(Request $request): void
    {
        abort_if($request->user()?->isAdmin(), 403);
    }
}
