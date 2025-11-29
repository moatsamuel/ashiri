<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'email',
        'ip_address',
        'successful',
        'user_agent',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'attempted_at' => 'datetime',
        ];
    }

    /**
     * Get failed attempts count for IP in last hour
     */
    public static function getFailedAttemptsForIp(string $ip, int $minutes = 60): int
    {
        return static::where('ip_address', $ip)
            ->where('successful', false)
            ->where('attempted_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Get failed attempts count for email in last hour
     */
    public static function getFailedAttemptsForEmail(string $email, int $minutes = 60): int
    {
        return static::where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>=', Carbon::now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Clear old attempts (run via scheduled task)
     */
    public static function clearOldAttempts(int $days = 30): void
    {
        static::where('attempted_at', '<', Carbon::now()->subDays($days))->delete();
    }
}