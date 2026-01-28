<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'news_id',
        'type',
        'status',
        'sent_at',
        'content'
    ];

    protected $casts = [
        'content' => 'array',
        'sent_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(Advertiser::class, 'user_id');
    }

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    // Scope for cleanup
    public function scopeOlderThan($query, $days)
    {
        return $query->where('created_at', '<', Carbon::now()->subDays($days));
    }

    // Get today's push notifications for user
    public function scopeTodayPushForUser($query, $userId)
    {
        return $query->where('user_id', $userId)
                    ->where('type', 'push')
                    ->where('status', 'sent')
                    ->whereDate('sent_at', Carbon::today());
    }
}