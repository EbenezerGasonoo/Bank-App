<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AdminActionApproval;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\TransactionEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminApprovalController extends Controller
{
    public function __construct(private TransactionEngine $engine)
    {
    }

    public function index(Request $request)
    {
        $category = $request->string('category')->toString();
        $allowedCategories = ['all', 'transfer', 'deposit', 'kyc', 'other'];
        if (!in_array($category, $allowedCategories, true)) {
            $category = 'all';
        }

        $approvalsQuery = AdminActionApproval::with(['requester', 'approver']);
        $this->applyCategoryFilter($approvalsQuery, $category);

        $approvals = $approvalsQuery
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $tabCounts = [];
        foreach ($allowedCategories as $tab) {
            $countQuery = AdminActionApproval::query();
            $this->applyCategoryFilter($countQuery, $tab);
            $tabCounts[$tab] = $countQuery->count();
        }

        return view('admin.approvals.index', compact('approvals', 'category', 'tabCounts'));
    }

    public function approve(Request $request, AdminActionApproval $approval)
    {
        $request->validate([
            'review_note' => 'nullable|string|max:500',
        ]);

        if ($approval->status !== 'pending') {
            return back()->withErrors(['approval' => 'This request has already been reviewed.']);
        }

        if ($approval->requested_by === $request->user()->id) {
            return back()->withErrors(['approval' => 'You cannot approve your own request.']);
        }

        DB::transaction(function () use ($approval, $request) {
            $this->executeAction($approval);

            $approval->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'review_note' => $request->review_note,
                'reviewed_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $request->user()->id,
                'action' => 'admin.approvals.approve',
                'model_type' => 'AdminActionApproval',
                'model_id' => $approval->id,
                'changes' => [
                    'approved_action' => $approval->action,
                    'target_type' => $approval->target_type,
                    'target_id' => $approval->target_id,
                    'payload' => $approval->payload,
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return back()->with('success', 'Approval accepted and action executed.');
    }

    public function reject(Request $request, AdminActionApproval $approval)
    {
        $request->validate([
            'review_note' => 'nullable|string|max:500',
        ]);

        if ($approval->status !== 'pending') {
            return back()->withErrors(['approval' => 'This request has already been reviewed.']);
        }

        DB::transaction(function () use ($approval, $request) {
            if ($approval->action === 'customer_kyc_request' && $approval->target_type === User::class) {
                $targetUser = User::find($approval->target_id);
                if ($targetUser) {
                    $targetUser->update(['kyc_status' => 'rejected']);
                }
            }

            $approval->update([
                'status' => 'rejected',
                'approved_by' => $request->user()->id,
                'review_note' => $request->review_note,
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', 'Approval request rejected.');
    }

    private function executeAction(AdminActionApproval $approval): void
    {
        $payload = $approval->payload ?? [];

        if ($approval->target_type === Account::class) {
            $account = Account::findOrFail($approval->target_id);

            match ($approval->action) {
                'credit_account' => $this->engine->deposit(
                    $account,
                    (float) ($payload['amount'] ?? 0),
                    (string) ($payload['description'] ?? 'Approved admin credit'),
                    $payload['reference'] ?? null,
                    !empty($payload['backdated_at']) ? Carbon::parse($payload['backdated_at']) : null
                ),
                'debit_account' => $this->engine->withdraw(
                    $account,
                    (float) ($payload['amount'] ?? 0),
                    (string) ($payload['description'] ?? 'Approved admin debit'),
                    $payload['reference'] ?? null,
                    !empty($payload['backdated_at']) ? Carbon::parse($payload['backdated_at']) : null
                ),
                'freeze_account' => $account->update(['status' => 'frozen']),
                'unfreeze_account' => $account->update(['status' => 'active']),
                'customer_deposit_request' => $this->engine->deposit(
                    $account,
                    (float) ($payload['amount'] ?? 0),
                    (string) ($payload['description'] ?? 'Approved customer deposit request')
                ),
                'customer_transfer_request' => $this->engine->withdraw(
                    $account,
                    (float) ($payload['amount'] ?? 0),
                    trim(sprintf(
                        'Approved international transfer | Beneficiary: %s | Bank: %s | Account/IBAN: %s | SWIFT: %s | Country: %s | Note: %s',
                        (string) ($payload['beneficiary_name'] ?? 'N/A'),
                        (string) ($payload['beneficiary_bank'] ?? 'N/A'),
                        (string) ($payload['beneficiary_account_number'] ?? 'N/A'),
                        (string) ($payload['swift_code'] ?? 'N/A'),
                        (string) ($payload['beneficiary_country'] ?? 'N/A'),
                        (string) ($payload['description'] ?? 'N/A')
                    ))
                ),
                default => throw new \RuntimeException('Unsupported approval action for account target.'),
            };

            return;
        }

        if ($approval->target_type === User::class) {
            $targetUser = User::findOrFail($approval->target_id);

            match ($approval->action) {
                'customer_kyc_request' => $targetUser->update(['kyc_status' => 'approved', 'account_status' => 'active']),
                default => throw new \RuntimeException('Unsupported approval action for user target.'),
            };

            return;
        }

        throw new \RuntimeException('Unsupported target type for approval action.');
    }

    private function applyCategoryFilter($query, string $category): void
    {
        if ($category === 'all') {
            return;
        }

        if ($category === 'transfer') {
            $query->whereIn('action', ['customer_transfer_request']);
            return;
        }

        if ($category === 'deposit') {
            $query->whereIn('action', ['customer_deposit_request']);
            return;
        }

        if ($category === 'kyc') {
            $query->whereIn('action', ['customer_kyc_request']);
            return;
        }

        $query->whereNotIn('action', [
            'customer_transfer_request',
            'customer_deposit_request',
            'customer_kyc_request',
        ]);
    }
}

