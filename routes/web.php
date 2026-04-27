<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreditScoreController;
use App\Http\Controllers\FdrController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\UserFeatureController;
use App\Http\Controllers\StatementController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\DpsController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\Admin\AdminWireCodeRequestController;
use App\Http\Controllers\Auth\LoginOtpController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminApprovalController;
use App\Http\Controllers\Admin\AdminSecurityController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminSystemController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;

// ─── Public Home ────────────────────────────────────────────
Route::get('/', function () {
    $announcements = Announcement::where('is_published', true)->latest()->take(3)->get();
    return view('welcome', compact('announcements'));
})->name('home');

// ─── Public marketing & info pages ──────────────────────────
$publicPaths = [
    'personal',
    'savings',
    'fdr',
    'business',
    'commercial',
    'customer-service',
    'security-center',
    'atms-and-branches',
    'loans',
    'wealth',
    'international',
    'support',
    'faq',
    'products/cards',
];
foreach ($publicPaths as $path) {
    Route::get($path, function () use ($path) {
        return app(PublicPageController::class)->show($path);
    })->name(PublicPageController::routeNameForKey($path));
}

// ─── Authenticated User Routes ───────────────────────────────
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/login-otp', [LoginOtpController::class, 'show'])->name('login.otp.show');
    Route::post('/login-otp/verify', [LoginOtpController::class, 'verify'])->name('login.otp.verify');
    Route::post('/login-otp/resend', [LoginOtpController::class, 'resend'])->name('login.otp.resend');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'otp.verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/credit-score', [CreditScoreController::class, 'show'])->name('credit-score.show');
    Route::get('/fdrs', [FdrController::class, 'index'])->name('fdrs.index');
    Route::post('/fdrs', [FdrController::class, 'store'])->name('fdrs.store');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transfer', [TransactionController::class, 'showTransfer'])->name('transactions.transfer');
    Route::get('/withdraw/crypto', [TransactionController::class, 'showCryptoWithdrawal'])->name('transactions.cryptoWithdrawal');
    Route::post('/transfer', [TransactionController::class, 'transfer'])->name('transactions.doTransfer');
    Route::get('/transfer/verify', [TransactionController::class, 'showVerifyTransfer'])->name('transactions.verifyTransfer');
    Route::post('/transfer/verify', [TransactionController::class, 'verifyTransfer'])->name('transactions.doVerifyTransfer');
    Route::post('/transfer/request-wire-codes', [TransactionController::class, 'requestWireCodes'])->name('transactions.requestWireCodes');
    Route::post('/transactions/request-deposit', [TransactionController::class, 'requestDeposit'])->name('transactions.requestDeposit');

    // Cards
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::post('/cards/{card}/toggle-freeze', [CardController::class, 'toggleFreeze'])->name('cards.toggleFreeze');

    // User feature pages
    Route::get('/features/{feature}', [UserFeatureController::class, 'show'])->name('features.show');
    Route::get('/statement', [StatementController::class, 'index'])->name('statement.index');
    Route::get('/statement/export', [StatementController::class, 'exportCsv'])->name('statement.export');
    Route::get('/loan', [LoanController::class, 'index'])->name('loan.index');
    Route::post('/loan', [LoanController::class, 'store'])->name('loan.store');
    Route::get('/dps', [DpsController::class, 'index'])->name('dps.index');
    Route::post('/dps', [DpsController::class, 'store'])->name('dps.store');
});

// ─── Admin Routes ────────────────────────────────────────────
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'otp.verified', 'admin', 'log.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/approve-kyc', [AdminUserController::class, 'approveKyc'])->name('users.approveKyc');
        Route::post('/users/{user}/reject-kyc', [AdminUserController::class, 'rejectKyc'])->name('users.rejectKyc');
        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('/users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');
        Route::post('/accounts/{account}/credit', [AdminUserController::class, 'creditAccount'])->name('accounts.credit');
        Route::post('/accounts/{account}/debit', [AdminUserController::class, 'debitAccount'])->name('accounts.debit');
        Route::post('/accounts/{account}/freeze', [AdminUserController::class, 'freezeAccount'])->name('accounts.freeze');
        Route::post('/accounts/{account}/unfreeze', [AdminUserController::class, 'unfreezeAccount'])->name('accounts.unfreeze');
        Route::delete('/accounts/{account}', [AdminUserController::class, 'deleteAccount'])->name('accounts.delete');

        // Transactions
        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transactions/{transaction}/flag', [AdminTransactionController::class, 'flag'])->name('transactions.flag');

        // Approvals
        Route::get('/approvals', [AdminApprovalController::class, 'index'])->name('approvals.index');
        Route::post('/approvals/{approval}/approve', [AdminApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{approval}/reject', [AdminApprovalController::class, 'reject'])->name('approvals.reject');

        // Security
        Route::get('/security/audit-logs', [AdminSecurityController::class, 'auditLogs'])->name('security.auditLogs');
        Route::get('/security/login-activity', [AdminSecurityController::class, 'loginActivity'])->name('security.loginActivity');

        // Wire code requests
        Route::get('/wire-requests', [AdminWireCodeRequestController::class, 'index'])->name('wire-requests.index');
        Route::post('/wire-requests/{wireRequest}/issue', [AdminWireCodeRequestController::class, 'issue'])->name('wire-requests.issue');

        // Announcements (CMS)
        Route::resource('announcements', AnnouncementController::class);

        // System management
        Route::get('/system-management', [AdminSystemController::class, 'index'])->name('system.index');
        Route::get('/system-management/{slug}', [AdminSystemController::class, 'show'])->name('system.show');
        Route::post('/system-management/{slug}', [AdminSystemController::class, 'update'])->name('system.update');
        Route::post('/system-management/{slug}/toggle', [AdminSystemController::class, 'toggle'])->name('system.toggle');
    });
