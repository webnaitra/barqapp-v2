<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
// use NotificationChannels\WebPush\HasPushSubscriptions;

class Advertiser extends Authenticatable
{
    use Notifiable; // HasPushSubscriptions;
    // use HasApiTokens;

    protected $table = 'advertisers';
    protected $hidden = ['adv_forgot_password_code', 'adv_reset_token', 'adv_password'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'adv_first_name',
        'adv_last_name',
        'adv_username',
        'adv_email',
        'image',
        'adv_password',
        'adv_mobile',
        'adv_forgot_password_code',
        'adv_verify_token',
        'adv_reset_token'
    ];

    public function setFirstNameAttribute($value)
    {
        $this->attributes['adv_first_name'] = $value;
    }

    // Mutator for adv_last_name
    public function setLastNameAttribute($value)
    {
        $this->attributes['adv_last_name'] = $value;
    }

    // Mutator for adv_username
    public function setUsernameAttribute($value)
    {
        $this->attributes['adv_username'] = $value;
    }

    // Mutator for adv_email
    public function setEmailAttribute($value)
    {
        $this->attributes['adv_email'] = $value;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['adv_password'] = $value;
    }

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

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'user_categories', 'user_id', 'category_id');
    }

    public function sources()
    {
        return $this->belongsToMany(Source::class, 'user_sources', 'user_id', 'source_id');
    }

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'user_keywords', 'user_id', 'keyword_id');
    }

    /**
     * Get the user's favorites
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'fav_user_id');
    }

    public function userCategories()
    {
        return $this->hasMany(UserCategory::class, 'user_id');
    }

    public function userKeywords()
    {
        return $this->hasMany(UserKeyword::class, 'user_id');
    }

    public function userSources()
    {
        return $this->hasMany(UserSource::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }

    /**
     * Get the user's favorite news items
     */
    public function favoriteNews()
    {
        return $this->hasManyThrough(
            News::class,
            Favorite::class,
            'fav_user_id', // Foreign key on favorites table
            'id',          // Foreign key on news_items table
            'id',          // Local key on advertisers table
            'fav_news_id'  // Local key on favorites table
        );
    }
}
