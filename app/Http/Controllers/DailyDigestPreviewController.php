<?php

namespace App\Http\Controllers;

use App\Mail\DailyDigestMail;
use App\Models\Advertiser;
use App\Models\News;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class DailyDigestPreviewController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Preview the Daily Digest email.
     *
     * @param  Request  $request
     * @param  string|null  $userId
     * @return \Illuminate\Mail\Mailable
     */
    public function preview(Request $request, $userId = null)
    {
        // Fetch a user (Advertiser)
        if ($userId) {
            $user = Advertiser::find($userId);
        } else {
            // Get the first advertiser who has email notifications enabled
            $user = Advertiser::where('email_notifications_enabled', true)->first();
        }

        if (!$user) {
            // Fallback mock user if no real user found
             $user = new Advertiser([
                'id' => 999,
                'adv_name' => 'Test User', 
                'adv_email' => 'test@example.com'
            ]);
             // For mock user, we can't fetch matching news via service easily if it depends on DB relations
             // So we might need to fetch generic news
        }

        // Fetch news using NotificationService if user exists in DB
        if ($user->exists) {
            $newsItems = $this->notificationService->getMatchingNewsForUser($user->id);
            
            // If no matching news, fallback to latest news just for preview purposes
            if ($newsItems->isEmpty()) {
                 $newsItems = News::latest()->with('category')->take(7)->get();
            }
        } else {
             $newsItems = News::latest()->with('category')->take(7)->get();
        }

        return new DailyDigestMail($user, $newsItems);
    }
}
