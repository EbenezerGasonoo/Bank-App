<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DpsRequest extends Model
{
    protected $fillable = [
        'user_id',
        'monthly_amount',
        'tenure_months',
        'interest_rate',
        'status',
        'plan_name',
        'notes',
    ];

    protected $casts = [
        'monthly_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
