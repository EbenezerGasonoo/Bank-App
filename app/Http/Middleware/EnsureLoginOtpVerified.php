<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLoginOtpVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        // Admin/staff roles bypass OTP; OTP is customer-only.
        if ($request->user()->isAdmin()) {
            $request->session()->put('login_otp_verified', true);
            return $next($request);
        }

        if ($request->session()->get('login_otp_verified') === true) {
            return $next($request);
        }

        return redirect()->route('login.otp.show');
    }
}

