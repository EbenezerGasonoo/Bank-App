<?php

namespace App\Actions\Fortify;

use App\Models\AdminActionApproval;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:1000'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (isset($input['id_document'])) {
            $path = $input['id_document']->store('kyc-documents', 'public');
            $user->forceFill([
                'id_document_path' => $path,
                'kyc_status' => 'pending',
            ])->save();

            if ($user->role === 'user') {
                AdminActionApproval::updateOrCreate(
                    [
                        'action' => 'customer_kyc_request',
                        'target_type' => User::class,
                        'target_id' => $user->id,
                        'status' => 'pending',
                    ],
                    [
                        'payload' => [
                            'id_document_path' => $path,
                            'phone' => $input['phone'] ?? $user->phone,
                            'date_of_birth' => $input['date_of_birth'] ?? optional($user->date_of_birth)->format('Y-m-d'),
                            'address' => $input['address'] ?? $user->address,
                        ],
                        'reason' => 'Customer KYC submission',
                        'requested_by' => $user->id,
                    ]
                );
            }
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'] ?? null,
                'date_of_birth' => $input['date_of_birth'] ?? null,
                'address' => $input['address'] ?? null,
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'] ?? null,
            'date_of_birth' => $input['date_of_birth'] ?? null,
            'address' => $input['address'] ?? null,
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
