<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fdr extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'principal',
        'annual_rate',
        'term_months',
        'payout_mode',
        'expected_interest',
        'maturity_amount',
        'status',
        'starts_at',
        'matures_at',
        'notes',
    ];

    protected $casts = [
        'principal' => 'decimal:2',
        'annual_rate' => 'decimal:2',
        'expected_interest' => 'decimal:2',
        'maturity_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'matures_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}

