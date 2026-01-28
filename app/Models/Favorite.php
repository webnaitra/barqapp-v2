<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'fav_user_id',
        'fav_news_id'
    ];

    /**
     * Get the user that owns the favorite
     */
    public function user()
    {
        return $this->belongsTo(Advertiser::class, 'fav_user_id');
    }

    /**
     * Get the news item that is favorited
     */
    public function newsItem()
    {
        return $this->belongsTo(News::class, 'fav_news_id');
    }

}
