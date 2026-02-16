<?php
namespace App\Services;

use Pusher\PushNotifications\PushNotifications;
use Illuminate\Support\Facades\Log;

class PusherService
{
    private $beamsClient;

    public function __construct()
    {
        $this->beamsClient = new PushNotifications([
            'instanceId' => config('services.pusher_beams.instance_id'),
            'secretKey' => config('services.pusher_beams.secret_key'),
        ]);
    }

    public function sendPushNotification($userId, $newsItem)
    {
        $interest = $userId ? "barqapp-updates-{$userId}" : ' barqapp-updates-guest';
        try {
            $data = [
                'title' => $newsItem->name,
                'body' => $newsItem->name,
                'news_id' => $newsItem->id,
                'image' => $newsItem->image,
                'category' => $newsItem->category->name ?? 'News',
                'timestamp' => now()->toISOString()
            ];


            $response = $this->beamsClient->publishToInterests(
                [$interest], // e.g., ['news', 'sports']
                [
                    'web' => [
                        'notification' => [
                            'title' => $data['title'],
                            'body' => $data['body'],
                        ]
                    ]
                ]
            );

            Log::info("Push notification sent to user {$userId} for news {$newsItem->id}", [
                'publish_id' => $response->publishId
            ]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error("Push notification failed for user {$userId}: " . $e->getMessage());
            return false;
        }
    }

    public function sendToInterest($interest, $newsItem)
    {
        try {
            $data = [
                'title' => $newsItem->name,
                'body' => 'New article in ' . ($newsItem->category->name ?? 'News'),
                'news_id' => $newsItem->id,
                'image' => $newsItem->news_image,
                'category' => $newsItem->category->name ?? 'News',
                'timestamp' => now()->toISOString()
            ];

            $response = $this->beamsClient->publishToInterests(
                [$interest], // e.g., ['news', 'sports']
                [
                    'web' => [
                        'notification' => [
                            'title' => $data['title'],
                            'body' => $data['body'],
                        ]
                    ]
                ]
            );

            Log::info("Push notification sent to interest {$interest} for news {$newsItem->id}");
            return $response;
        } catch (\Exception $e) {
            Log::error("Push notification failed for interest {$interest}: " . $e->getMessage());
            return false;
        }
    }
}