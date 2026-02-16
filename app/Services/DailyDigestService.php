<?php

namespace App\Services;

use App\Models\Advertiser;
use App\Services\NotificationService;
use App\Mail\DailyDigestMail;
use Illuminate\Support\Facades\Mail;

class DailyDigestService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send daily digest to all eligible users.
     */
    public function sendDigestToAll()
    {
        $users = $this->notificationService->getEligibleUsersForDigest();

        foreach ($users as $user) {
            $this->sendDigestToUser($user->id);
        }
    }

    /**
     * Send daily digest to a single user by ID.
     *
     * @param int $userId
     */
    public function sendDigestToUser(int $userId)
    {
        $user = Advertiser::find($userId);
        if (!$user) {
            return;
        }

        $newsItems = $this->notificationService->getMatchingNewsForUser($user->id);

        if ($newsItems->isNotEmpty()) {
            Mail::to($user->adv_email)->send(new DailyDigestMail($user, $newsItems));
            $user->update(['last_email_digest_sent' => now()]);
        }
    }
}

