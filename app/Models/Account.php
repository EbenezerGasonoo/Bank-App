<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'type',
        'balance',
        'status',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'sender_account_id');
    }

    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'receiver_account_id');
    }

    public function ledgerDebits(): HasMany
    {
        return $this->hasMany(LedgerEntry::class, 'debit_account_id');
    }

    public function ledgerCredits(): HasMany
    {
        return $this->hasMany(LedgerEntry::class, 'credit_account_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function fdrs(): HasMany
    {
        return $this->hasMany(Fdr::class);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return '£' . number_format($this->balance, 2);
    }
}
