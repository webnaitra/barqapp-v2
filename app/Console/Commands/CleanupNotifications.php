<?php

namespace App\Console\Commands;

use App\Models\UserNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupNotifications extends Command
{
    protected $signature = 'notifications:cleanup {--days=4}';
    protected $description = 'Clean up old notification records';

    public function handle()
    {
        $days = $this->option('days');
        $deletedCount = UserNotification::olderThan($days)->delete();
        
        $this->info("Deleted {$deletedCount} notification records older than {$days} days.");
        Log::info("Notification cleanup completed. Deleted {$deletedCount} records.");
    }
}