<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\LoginOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginOtpController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;

    public function show()
    {
        return view('auth.login-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (!$user || !$user->login_otp_code || !$user->login_otp_expires_at) {
            return back()->withErrors(['otp' => 'No OTP request found. Please request a new code.']);
        }

        if (now()->greaterThan($user->login_otp_expires_at)) {
            return back()->withErrors(['otp' => 'This OTP has expired. Please request a new code.']);
        }

        if (!Hash::check($request->otp, $user->login_otp_code)) {
            return back()->withErrors(['otp' => 'Invalid OTP code.']);
        }

        $user->forceFill([
            'login_otp_code' => null,
            'login_otp_expires_at' => null,
        ])->save();

        $request->session()->put('login_otp_verified', true);

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $plainOtp = (string) random_int(100000, 999999);

        $user->forceFill([
            'login_otp_code' => Hash::make($plainOtp),
            'login_otp_expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ])->save();

        $user->notify(new LoginOtpNotification($plainOtp, self::OTP_EXPIRY_MINUTES));

        return back()->with('status', 'A new OTP has been sent to your email.');
    }
}

