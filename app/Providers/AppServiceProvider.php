<?php

namespace App\Providers;

use App\Models\LoginActivity;
use App\Notifications\LoginOtpNotification;
use App\Notifications\WelcomeMessageNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->isProduction()) {
            // Ignore an accidentally deployed public/hot file in production.
            Vite::useHotFile(storage_path('framework/vite.hot'));
        }

        Event::listen(Login::class, function (Login $event): void {
            $request = request();

            LoginActivity::create([
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'role' => $event->user->role,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
                'logged_in_at' => now(),
            ]);

            if ($event->user->isAdmin()) {
                $event->user->forceFill([
                    'login_otp_code' => null,
                    'login_otp_expires_at' => null,
                ])->save();

                if ($request) {
                    $request->session()->put('login_otp_verified', true);
                }

                return;
            }

            $plainOtp = (string) random_int(100000, 999999);

            $event->user->forceFill([
                'login_otp_code' => Hash::make($plainOtp),
                'login_otp_expires_at' => now()->addMinutes(10),
            ])->save();

            if ($request) {
                $request->session()->put('login_otp_verified', false);
            }

            $event->user->notify(new LoginOtpNotification($plainOtp, 10));
        });

        Event::listen(Verified::class, function (Verified $event): void {
            if ($event->user->accounts()->exists()) {
                $event->user->notify(new WelcomeMessageNotification());
            }
        });
    }
}
