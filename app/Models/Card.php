<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'card_number_masked',
        'card_number_encrypted',
        'expiration',
        'cvv_encrypted',
        'cardholder_name',
        'is_frozen',
    ];

    protected $casts = [
        'is_frozen' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function getCardNumberAttribute(): string
    {
        return Crypt::decryptString($this->card_number_encrypted);
    }
}
