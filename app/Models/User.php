<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmail;
    use HasApiTokens;
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'wire_pin_hash',
        'tax_code_hash',
        'imf_code_hash',
        'cot_code_hash',
        'phone',
        'date_of_birth',
        'address',
        'id_document_path',
        'kyc_status',
        'account_status',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'wire_pin_hash',
        'tax_code_hash',
        'imf_code_hash',
        'cot_code_hash',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'login_otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function fdrs(): HasMany
    {
        return $this->hasMany(Fdr::class);
    }

    public function primaryAccount()
    {
        return $this->accounts()->first();
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'support', 'auditor']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function hasWireAuthCodesConfigured(): bool
    {
        return !empty($this->wire_pin_hash)
            && !empty($this->tax_code_hash)
            && !empty($this->imf_code_hash)
            && !empty($this->cot_code_hash);
    }

    public function validateWireAuthCodes(string $pin, string $taxCode, string $imfCode, string $cotCode): bool
    {
        return Hash::check($pin, (string) $this->wire_pin_hash)
            && Hash::check($taxCode, (string) $this->tax_code_hash)
            && Hash::check($imfCode, (string) $this->imf_code_hash)
            && Hash::check($cotCode, (string) $this->cot_code_hash);
    }
}
