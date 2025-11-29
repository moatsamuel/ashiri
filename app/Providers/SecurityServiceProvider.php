<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\LoginAttempt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SecurityServiceProvider extends ServiceProvider
{
    const MAX_LOGIN_ATTEMPTS_PER_IP = 10;
    const MAX_LOGIN_ATTEMPTS_PER_EMAIL = 5;
    const LOCKOUT_DURATION_MINUTES = 30;
    const ATTEMPT_WINDOW_MINUTES = 60;

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('security', function ($app) {
            return $this;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Check if IP is locked out
     */
    public function isIpLockedOut(string $ip): bool
    {
        $cacheKey = "lockout:ip:{$ip}";
        
        if (Cache::has($cacheKey)) {
            return true;
        }

        $attempts = LoginAttempt::getFailedAttemptsForIp($ip, self::ATTEMPT_WINDOW_MINUTES);
        
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS_PER_IP) {
            Cache::put($cacheKey, true, now()->addMinutes(self::LOCKOUT_DURATION_MINUTES));
            return true;
        }

        return false;
    }

    /**
     * Check if email is locked out
     */
    public function isEmailLockedOut(string $email): bool
    {
        $cacheKey = "lockout:email:{$email}";
        
        if (Cache::has($cacheKey)) {
            return true;
        }

        $attempts = LoginAttempt::getFailedAttemptsForEmail($email, self::ATTEMPT_WINDOW_MINUTES);
        
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS_PER_EMAIL) {
            Cache::put($cacheKey, true, now()->addMinutes(self::LOCKOUT_DURATION_MINUTES));
            return true;
        }

        return false;
    }

    /**
     * Record login attempt
     */
    public function recordLoginAttempt(string $email, string $ip, bool $successful, ?string $userAgent = null): void
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ip,
            'successful' => $successful,
            'user_agent' => $userAgent,
            'attempted_at' => Carbon::now(),
        ]);
    }

    /**
     * Get remaining lockout time in minutes
     */
    public function getRemainingLockoutTime(string $identifier, string $type = 'ip'): ?int
    {
        $cacheKey = "lockout:{$type}:{$identifier}";
        
        if (Cache::has($cacheKey)) {
            // For file/array cache, return default duration
            return self::LOCKOUT_DURATION_MINUTES;
        }

        return null;
    }

    /**
     * Clear lockout for identifier
     */
    public function clearLockout(string $identifier, string $type = 'ip'): void
    {
        $cacheKey = "lockout:{$type}:{$identifier}";
        Cache::forget($cacheKey);
    }
}