<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\PusherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPushNotifications extends Command
{
    protected $signature = 'notifications:send-push';
    protected $description = 'Send push notifications to eligible users';

    public function handle()
    {
        $notificationService = new NotificationService();
        $pusherService = new PusherService();
        
        $users = $notificationService->getEligibleUsersForPush();
        $sentCount = 0;

        foreach ($users as $user) {
            try {
                if (!$notificationService->canSendPushNotification($user->id)) {
                    continue;
                }

                $availableNews = $notificationService->getAvailablePushNotificationsForUser($user->id);
                
                if ($availableNews->isEmpty()) {
                    continue;
                }

                // Pick random news item
                $selectedNews = $availableNews->random();
                
                $success = $pusherService->sendPushNotification($user->id, $selectedNews);
                
                if ($success) {
                    $notificationService->markNotificationSent(
                        $user->id,
                        $selectedNews->id,
                        'push',
                        ['title' => $selectedNews->name]
                    );
                    $sentCount++;
                } else {
                    $notificationService->markNotificationFailed(
                        $user->id,
                        $selectedNews->id,
                        'push',
                        ['title' => $selectedNews->name]
                    );
                }
                
            } catch (\Exception $e) {
                Log::error("Error sending push notification to user {$user->id}: " . $e->getMessage());
            }
        }

        $this->info("Push notifications sent: {$sentCount}");
        Log::info("Push notification command completed. Sent {$sentCount} notifications.");
    }
}