<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\KeywordFilterScope;

class Keyword extends Model
{

    protected static function booted(){
        static::addGlobalScope(new KeywordFilterScope);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $appends = array('followers', 'subscribed');
    protected $fillable = [
        'keyword_name', 'image', 'type' , 'short_description', 'description', 'category_id'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */

    /**
     * Get the list of tags attached to the article.
     *
     * @return array
     */
    public function media(){
        return $this->hasOne(Media::class, 'id', 'image');
    }

    public function users()
    {
        return $this->belongsToMany(Advertiser::class, 'user_keywords', 'keyword_id', 'user_id');
    }

    public function getFollowersAttribute()
    {
        return $this->users()->count();
    }

    public function getSubscribedAttribute()
    {
        $userId = request()->header('X-User-ID');
        if (!$userId) {
            return false;
        }
        return $this->users()->where('user_id', $userId)->exists();
    }

    public function news()
    {
        return $this->belongsToMany(News::class, 'news_keywords', 'keyword_id', 'news_id');
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_keyword','keyword_id','country_id');
    }
    
}
