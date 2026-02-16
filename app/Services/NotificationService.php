<?php

namespace App\Services;

use App\Models\Advertiser;
use App\Models\News;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class NotificationService
{
    public function getMatchingNewsForUser($userId, $days = 1)
    {
        $user = Advertiser::find($userId);
        if (!$user) return collect();

        // Get user preferences
        $userCategories = $user->userCategories()->pluck('categories.id')->toArray();
        $userKeywords = $user->userKeywords()->pluck('keywords.id')->toArray();
        $userSources = $user->userSources()->pluck('sources.id')->toArray();

        if (empty($userCategories) && empty($userKeywords) && empty($userSources)) {
            return collect();
        }

        $query = News::with(['category']);

        // Filter by date if needed (e.g. for digest)
        if ($days > 0) {
            $query->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        // First try: Match ALL criteria (AND logic)
        $strictMatching = clone $query;
        if (!empty($userCategories)) {
            $strictMatching->whereIn('category_id', $userCategories);
        }
        
        if (!empty($userSources)) {
            $strictMatching->whereIn('source_id', $userSources);
        }

        // Keywords check (if implemented in News model as belongsToMany)
        if (!empty($userKeywords)) {
             $strictMatching->whereHas('keywords', function($q) use ($userKeywords) {
                 $q->whereIn('keywords.id', $userKeywords); // Explicit table name just in case
             });
        }

        $strictResults = $strictMatching->limit(10)->get();


        if ($strictResults->count() >= 10) {
            return $strictResults;
        }

        // Fallback: Match within selected categories with OR logic
        // This logic mimics the original BUT adapted for new columns
        // Originally it was: Matches Category AND (Keyword OR Source)
        // Let's keep the spirit: User MUST match category, then maybe source or keyword
        
        $flexibleQuery = clone $query;
        $flexibleQuery->where(function($q) use ($userCategories, $userKeywords, $userSources) {
            
            // If user has categories, we prioritize news from those categories
            if (!empty($userCategories)) {
                $q->whereIn('category_id', $userCategories);
                
                // Inside these categories, try to find matches for keywords or sources
                // Note: The original logic seemed to allow OR behavior. 
                // Let's simplify: Get news from user's categories OR user's sources OR user's keywords?
                // The original code had nested specific logic. 
                // Let's try a broader approach:
                // (Category IN userCats) OR (Source IN userSources) OR (Keywords IN userKeywords)
                
                $orConditions = function($subQ) use ($userCategories, $userKeywords, $userSources) {
                     // Check if news has matching keywords (and is in user categories if desired, or just global?)
                     // Original code enforced "whereHas('news', ... category)" which implies looking for News via pivot?
                     // Let's simplify to: News that matches Keywords OR News that matches Sources
                     
                     if (!empty($userKeywords)) {
                        $subQ->orWhereHas('keywords', function($kQ) use ($userKeywords) {
                            $kQ->whereIn('keywords.id', $userKeywords);
                        });
                     }
                     if (!empty($userSources)) {
                        $subQ->orWhereIn('source_id', $userSources);
                     }
                };

                // The original code was: WHERE category IN userCats AND ( ... OR ... )
                // But looking closely at original:
                // $q->whereIn('news_cat_id', $userCategories);
                // if keyword: $q->orWhereHas(...) -> this OR is attached to the main grouping? 
                // Wait, $q is the main group. 
                // $q->whereIn(...) -> AND condition.
                // $q->orWhereHas(...) -> This makes it: (Category match) OR (Keyword match)
                // This seems to be what was intended.

                if (!empty($userKeywords) || !empty($userSources)) {
                     $q->orWhere(function($sub) use ($userKeywords, $userSources) {
                        if (!empty($userKeywords)) {
                            $sub->orWhereHas('keywords', function($kQ) use ($userKeywords) {
                                $kQ->whereIn('keywords.id', $userKeywords);
                            });
                        }
                        if (!empty($userSources)) {
                            $sub->orWhereIn('source_id', $userSources);
                        }
                     });
                }
            } else {
                // If no categories selected, match ANY source or keyword
                 if (!empty($userKeywords)) {
                    $q->orWhereHas('keywords', function($kQ) use ($userKeywords) {
                        $kQ->whereIn('keywords.id', $userKeywords);
                    });
                }
                if (!empty($userSources)) {
                    $q->orWhereIn('source_id', $userSources);
                }
            }
        });

        return $flexibleQuery->limit(20)->get();
    }

    public function getEligibleUsersForDigest()
    {
        return Advertiser::where('email_notifications_enabled', true)
            ->where(function($q) {
                $q->whereNull('last_email_digest_sent')
                  ->orWhere('last_email_digest_sent', '<', Carbon::today());
            })
            ->get();
    }

    public function getEligibleUsersForPush()
    {
        return Advertiser::where('push_notifications_enabled', true)->get();
    }

    public function getAvailablePushNotificationsForUser($userId)
    {
        // Get news from broader timeframe (last 7 days) for push notifications
        $matchingNews = $this->getMatchingNewsForUser($userId, 7);
        
        // Exclude already sent push notifications
        $sentNewsIds = UserNotification::where('user_id', $userId)
            ->where('type', 'push')
            ->where('status', 'sent')
            ->whereDate('sent_at', Carbon::today())
            ->pluck('news_id')
            ->toArray();

        return $matchingNews->whereNotIn('id', $sentNewsIds);
    }

    public function canSendPushNotification($userId)
    {
        $todayCount = UserNotification::todayPushForUser($userId)->count();
        
        if ($todayCount >= 4) {
            return false;
        }

        // Check last notification time (3 hours gap)
        $lastNotification = UserNotification::where('user_id', $userId)
            ->where('type', 'push')
            ->where('status', 'sent')
            ->latest('sent_at')
            ->first();

        if ($lastNotification && $lastNotification->sent_at->diffInHours(Carbon::now()) < 3) {
            return false;
        }

        // Check time window (8 AM to 10 PM)
        $currentHour = Carbon::now()->hour;
        return $currentHour >= 8 && $currentHour < 22;
    }

    public function markNotificationSent($userId, $newsId, $type, $content = null)
    {
        return UserNotification::create([
            'user_id' => $userId,
            'news_id' => $newsId,
            'type' => $type,
            'status' => 'sent',
            'sent_at' => Carbon::now(),
            'content' => $content
        ]);
    }

    public function markNotificationFailed($userId, $newsId, $type, $content = null)
    {
        return UserNotification::create([
            'user_id' => $userId,
            'news_id' => $newsId,
            'type' => $type,
            'status' => 'failed',
            'content' => $content
        ]);
    }
}