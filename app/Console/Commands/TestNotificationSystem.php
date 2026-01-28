<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\PusherService;
use App\Models\Advertiser;
use App\Models\UserNotification;
use Illuminate\Console\Command;

class TestNotificationSystem extends Command
{
    protected $signature = 'test:notifications {--user=} {--type=all}';
    protected $description = 'Test the notification system';

    public function handle()
    {
        $userId = $this->option('user');
        $type = $this->option('type');

        $notificationService = new NotificationService();
        $pusherService = new PusherService();

        $this->info('🔄 Testing Notification System...');

        // Test 1: User matching
        if (in_array($type, ['all', 'matching'])) {
            $this->info("\n📊 Testing user preference matching...");
            
            if ($userId) {
                $users = collect([Advertiser::find($userId)]);
            } else {
                $users = Advertiser::limit(3)->get();
            }

            foreach ($users as $user) {
                if (!$user) continue;
                
                $matchingNews = $notificationService->getMatchingNewsForUser($user->id, 1);
                $this->line("User {$user->id} ({$user->email}): {$matchingNews->count()} matching news items");
            }
        }

        // Test 2: Email digest
        if (in_array($type, ['all', 'email'])) {
            $this->info("\n📧 Testing email digest...");
            
            if ($userId) {
                $user = Advertiser::find($userId);
                if ($user && $user->email_notifications_enabled) {
                    $matchingNews = $notificationService->getMatchingNewsForUser($user->id, 1);
                    if ($matchingNews->count() > 0) {
                        $this->line($success ? "✅ Email sent to {$user->email}" : "❌ Failed to send email to {$user->email}");
                    } else {
                        $this->line("⚠️ No matching news for user {$user->email}");
                    }
                }
            } else {
                $this->line("Please specify --user=ID to test email sending");
            }
        }

        // Test 3: Push notification
        if (in_array($type, ['all', 'push'])) {
            $this->info("\n🔔 Testing push notification...");
            
            if ($userId) {
                $user = Advertiser::find($userId);
                if ($user && $user->push_notifications_enabled) {
                    $availableNews = $notificationService->getAvailablePushNotificationsForUser($user->id);
                    if ($availableNews->count() > 0) {
                        $newsItem = $availableNews->first();
                        $success = $pusherService->sendPushNotification($user->id, $newsItem);
                        $this->line($success ? "✅ Push notification sent to user {$user->id}" : "❌ Failed to send push notification");
                    } else {
                        $this->line("⚠️ No available news for push notification");
                    }
                }
            } else {
                $this->line("Please specify --user=ID to test push notification");
            }
        }

        // Test 4: Statistics
        if (in_array($type, ['all', 'stats'])) {
            $this->info("\n📈 Notification Statistics:");
            $total = UserNotification::count();
            $today = UserNotification::whereDate('created_at', today())->count();
            $email = UserNotification::where('type', 'email')->count();
            $push = UserNotification::where('type', 'push')->count();
            
            $this->line("Total notifications: {$total}");
            $this->line("Today's notifications: {$today}");
            $this->line("Email notifications: {$email}");
            $this->line("Push notifications: {$push}");
        }

        $this->info("\n✅ Testing completed!");
    }
}
