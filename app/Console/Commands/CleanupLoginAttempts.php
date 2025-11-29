<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoginAttempt;

class CleanupLoginAttempts extends Command
{
    protected $signature = 'cleanup:login-attempts';
    protected $description = 'Clean up old login attempts';

    public function handle()
    {
        LoginAttempt::clearOldAttempts(30);
        $this->info('Old login attempts cleaned up successfully.');
    }
}