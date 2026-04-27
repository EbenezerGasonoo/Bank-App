<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AdminActionApproval;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\AccountSuspendedNotification;
use App\Services\AccountService;
use App\Services\TransactionEngine;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    private const SENSITIVE_AMOUNT_THRESHOLD = 10000;

    public function __construct(
        private AccountService $accountService,
        private TransactionEngine $engine
    ) {}

    public function index(Request $request)
    {
        $query = User::where('role', 'user')->with('accounts');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        if ($request->kyc_status) {
            $query->where('kyc_status', $request->kyc_status);
        }

        if ($request->email_verified === '1') {
            $query->whereNotNull('email_verified_at');
        } elseif ($request->email_verified === '0') {
            $query->whereNull('email_verified_at');
        }

        if ($request->mobile_verified === '1') {
            $query->whereNotNull('phone');
        } elseif ($request->mobile_verified === '0') {
            $query->whereNull('phone');
        }

        if ($request->account_status) {
            $query->where('account_status', $request->account_status);
        }

        if ($request->profile_state === 'complete') {
            $query->whereNotNull('phone')
                ->whereNotNull('date_of_birth')
                ->whereNotNull('address')
                ->whereNotNull('id_document_path');
        } elseif ($request->profile_state === 'incomplete') {
            $query->where(function ($q) {
                $q->whereNull('phone')
                    ->orWhereNull('date_of_birth')
                    ->orWhereNull('address')
                    ->orWhereNull('id_document_path');
            });
        }

        if ($request->kyc_bucket === 'verified') {
            $query->where('kyc_status', 'approved');
        } elseif ($request->kyc_bucket === 'unverified') {
            $query->whereIn('kyc_status', ['pending', 'rejected']);
        } elseif ($request->kyc_bucket === 'pending') {
            $query->where('kyc_status', 'pending');
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $baseUsers = User::where('role', 'user');
        $profileComplete = (clone $baseUsers)
            ->whereNotNull('phone')
            ->whereNotNull('date_of_birth')
            ->whereNotNull('address')
            ->whereNotNull('id_document_path')
            ->count();

        $userCounts = [
            'all' => (clone $baseUsers)->count(),
            'profile_incomplete' => (clone $baseUsers)
                ->where(function ($q) {
                    $q->whereNull('phone')
                        ->orWhereNull('date_of_birth')
                        ->orWhereNull('address')
                        ->orWhereNull('id_document_path');
                })->count(),
            'profile_complete' => $profileComplete,
            'active' => (clone $baseUsers)->where('account_status', 'active')->count(),
            'banned' => (clone $baseUsers)->where('account_status', 'suspended')->count(),
            'email_unverified' => (clone $baseUsers)->whereNull('email_verified_at')->count(),
            'email_verified' => (clone $baseUsers)->whereNotNull('email_verified_at')->count(),
            'mobile_unverified' => (clone $baseUsers)->whereNull('phone')->count(),
            'mobile_verified' => (clone $baseUsers)->whereNotNull('phone')->count(),
            'kyc_unverified' => (clone $baseUsers)->whereIn('kyc_status', ['pending', 'rejected'])->count(),
            'kyc_verified' => (clone $baseUsers)->where('kyc_status', 'approved')->count(),
            'kyc_pending' => (clone $baseUsers)->where('kyc_status', 'pending')->count(),
        ];

        return view('admin.users.index', compact('users', 'userCounts'));
    }

    public function show(User $user)
    {
        $user->load(['accounts.cards', 'accounts.sentTransactions', 'accounts.receivedTransactions']);
        return view('admin.users.show', compact('user'));
    }

    public function approveKyc(User $user)
    {
        $user->update(['kyc_status' => 'approved', 'account_status' => 'active']);
        // Create a checking account if user doesn't have one
        if ($user->accounts()->count() === 0) {
            $this->accountService->createAccount($user, 'checking');
        }
        return back()->with('success', 'KYC approved and account created.');
    }

    public function rejectKyc(User $user)
    {
        $user->update(['kyc_status' => 'rejected']);
        return back()->with('success', 'KYC rejected.');
    }

    public function suspend(User $user)
    {
        $user->update(['account_status' => 'suspended']);
        $user->notify(new AccountSuspendedNotification());
        return back()->with('success', 'User account suspended.');
    }

    public function activate(User $user)
    {
        $user->update(['account_status' => 'active']);
        return back()->with('success', 'User account activated.');
    }

    public function creditAccount(Request $request, Account $account)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:50|unique:transactions,reference',
            'backdated_at' => 'nullable|date|before_or_equal:now',
        ]);
        $amount = (float) $request->amount;
        $description = $request->description ?? 'Admin Credit';
        $reference = $request->filled('reference') ? trim((string) $request->reference) : null;
        $backdatedAt = $request->filled('backdated_at') ? Carbon::parse($request->backdated_at) : null;

        if (
            $amount >= self::SENSITIVE_AMOUNT_THRESHOLD
            && !$request->user()->isSuperAdmin()
        ) {
            $this->queueApproval($request, $account, 'credit_account', [
                'amount' => $amount,
                'description' => $description,
                'reference' => $reference,
                'backdated_at' => $backdatedAt?->toDateTimeString(),
            ]);

            return back()->with('success', 'Credit request queued for super admin approval.');
        }

        $this->engine->deposit($account, $amount, $description, $reference, $backdatedAt);
        return back()->with('success', '£' . number_format($amount, 2) . ' credited successfully.');
    }

    public function debitAccount(Request $request, Account $account)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:50|unique:transactions,reference',
            'backdated_at' => 'nullable|date|before_or_equal:now',
        ]);
        $amount = (float) $request->amount;
        $description = $request->description ?? 'Admin Debit';
        $reference = $request->filled('reference') ? trim((string) $request->reference) : null;
        $backdatedAt = $request->filled('backdated_at') ? Carbon::parse($request->backdated_at) : null;

        if ($amount >= self::SENSITIVE_AMOUNT_THRESHOLD) {
            $this->queueApproval($request, $account, 'debit_account', [
                'amount' => $amount,
                'description' => $description,
                'reference' => $reference,
                'backdated_at' => $backdatedAt?->toDateTimeString(),
            ]);

            return back()->with('success', 'Debit request queued for admin approval.');
        }

        try {
            $this->engine->withdraw($account, $amount, $description, $reference, $backdatedAt);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }
        return back()->with('success', '£' . number_format($amount, 2) . ' debited successfully.');
    }

    public function freezeAccount(Request $request, Account $account)
    {
        $this->queueApproval($request, $account, 'freeze_account');
        return back()->with('success', 'Freeze request queued for admin approval.');
    }

    public function unfreezeAccount(Request $request, Account $account)
    {
        $this->queueApproval($request, $account, 'unfreeze_account');
        return back()->with('success', 'Unfreeze request queued for admin approval.');
    }

    public function deleteAccount(Request $request, Account $account)
    {
        if (!$request->user()->isSuperAdmin()) {
            abort(403, 'Only super admins can delete accounts.');
        }

        if ((float) $account->balance > 0) {
            return back()->withErrors([
                'account_delete' => 'Only accounts with a zero balance can be deleted.',
            ]);
        }

        if ($account->sentTransactions()->exists() || $account->receivedTransactions()->exists()) {
            return back()->withErrors([
                'account_delete' => 'Accounts with transaction history cannot be deleted.',
            ]);
        }

        if ($account->ledgerDebits()->exists() || $account->ledgerCredits()->exists()) {
            return back()->withErrors([
                'account_delete' => 'Accounts with ledger history cannot be deleted.',
            ]);
        }

        if ($account->fdrs()->exists()) {
            return back()->withErrors([
                'account_delete' => 'Please close linked fixed deposits before deleting this account.',
            ]);
        }

        $admin = $request->user();
        $accountId = $account->id;
        $accountNumber = $account->account_number;

        DB::transaction(function () use ($admin, $request, $account, $accountId, $accountNumber): void {
            AdminActionApproval::where('target_type', Account::class)
                ->where('target_id', $accountId)
                ->where('status', 'pending')
                ->delete();

            $account->delete();

            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'admin.accounts.deleted',
                'model_type' => 'Account',
                'model_id' => $accountId,
                'changes' => [
                    'account_number' => $accountNumber,
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return back()->with('success', 'Account deleted successfully.');
    }

    private function queueApproval(Request $request, Account $account, string $action, array $payload = []): void
    {
        AdminActionApproval::create([
            'action' => $action,
            'target_type' => Account::class,
            'target_id' => $account->id,
            'payload' => $payload ?: null,
            'reason' => $request->input('description'),
            'requested_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.approvals.requested',
            'model_type' => 'Account',
            'model_id' => $account->id,
            'changes' => [
                'requested_action' => $action,
                'payload' => $payload,
            ],
            'ip_address' => $request->ip(),
        ]);
    }
}
