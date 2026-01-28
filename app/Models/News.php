<?php

namespace App\Models;
use App\Models\Scopes\NewsFilterScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class News extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'news';
    protected $appends = array('image_url', 'favorite', 'has_source_logo', 'source_icon', 'source');
    protected $fillable = [
        'name', 'content', 'image', 'category_id',
        'views', 'shares', 'urgent', 'video', 'site', 'source_link', 'slug','date','excerpt'
    ];

    //protected $appends = ['source_icon'];

    protected static function booted()
    {
        static::addGlobalScope(new NewsFilterScope);
    }


    /**
     * Get all of the posts that are assigned this tag.
     */
    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id');
     }

    public function favorites_list(){
        return $this->belongsTo(Favorite::class, 'id', 'fav_news_id');        
    }

    /**
     * Get the favorites for the news item
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'fav_news_id');
    }


    /**
     * Check if the news item is favorited by the authenticated user
     */
    public function getIsFavoritedAttribute()
    {
        $user = auth('api')->user();
        if (!$user) {
            return false;
        }
        
        return $this->favorites()->where('fav_user_id', $user->id)->exists();
    }


    /**
     * Get all of the posts that are assigned this tag.
     */
    public function sources()
    {
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    
    /**
     * Append a custom attribute to the model source that will return the source name
     */
    public function getSourceAttribute()
    {
        return $this->sources->arabic_name ?? null;
    }

    /**
     * Check if the news item is favorited by the authenticated user
     */
    public function getSourceIconAttribute()
    {
        return !empty($this->sources->logo) ? env('CRON_URL').'storage/'.$this->sources->logo : url('/images/barqapp_placeholder.jpg');
    }

    public function getImageAttribute($value)
    {
        if($value) {
            return $value;
        }
        if(!$this->sources) {
            return url('/images/barqapp_placeholder_large.jpg');
        }
        $source_image = $this->sources->placeholder_image ? env('CRON_URL').'storage/'.$this->sources->placeholder_image : url('/images/barqapp_placeholder_large.jpg');
        return  $source_image;
    }

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function tags(){
        return $this->belongsToMany(Tag::class, 'news_tags', 'news_id', 'tag_id');
    }

    /**
     * Get all of the posts that are assigned this keywords.
     */
    public function keywords(){
        return $this->belongsToMany(Keyword::class, 'news_keywords', 'news_id', 'keyword_id');
    }



    public function getImageUrlAttribute()
    {
        if (filter_var($this->image, FILTER_VALIDATE_URL) === FALSE) {
            return url('storage/app').'/'.$this->image;
        }

        if (strpos($this->image, 'skynewsarabia.com') !== false) {
            $needle = "/95/53";
            $replacement = "/900/506";
            $url = str_replace($needle, $replacement, $this->image);
        } else {
            $url = $this->image;
        }
        return $url;
    }

    public function getNewsExcerptAttribute($value)
    {
        $value = html_entity_decode($value);
        return strip_tags($value);
    }

    public function getHasSourceLogoAttribute()
    {
         
        if (str_contains($this->news_image, 'cronapp.jawlate.com')|| str_contains($this->news_image, 'cronapp.barqapp.net')) { 
            return true;
        }
        return false;
    }

    public function getFavoriteAttribute()
    {
        if (request()->header('X-User-ID')) {
            return $this->favorites_list()->where('fav_user_id', request()->header('X-User-ID'))->count() > 0;
        } else {
            return false;
        }
    }

    

    public function getNewsTitleAttribute($value)
    {
        return html_entity_decode($value);
    }

    public function getRelatedNewsAttribute()
    {
        if (!$this->category) {
            return collect(); 
        }

        $categoryId = $this->category->id;

        return self::where('id', '!=', $this->id) 
            ->where(function ($query) use ($categoryId) {
                $query->whereHas('tags', function ($tagQuery) {
                    $tagQuery->whereIn('tags.id', $this->tags->pluck('id'));
                })
                ->orWhere(function ($query) use ($categoryId) {
                    $query->doesntHave('tags')
                        ->where('category_id', $categoryId);
                });
            })
        ->select("id", 'name','slug', 'image', 'category_id', 'source_id', 'urgent','date','video', 'source_link','created_at')
        ->take(5) 
        ->get();
    }

}
