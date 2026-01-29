<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\SourceFilterScope;
use App\Models\News;

class Source extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = 'sources';
    protected $appends = ['logo_url', 'placeholder_image_url', 'followers'];
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'arabic_name',
        'country_id',
        'source_type_id',
        'description', 
        'website',
        'phone',
        'email',  
        'placeholder_image',
        'logo',
        'filter_classes',
        'content_classes',
        'image_classes',
        'freeze'
    ];

    public function getLogoUrlAttribute()
    {
        // Return the related media image or null if not available
        return $this->logo ? env('CRON_URL').'storage/'.$this->logo : url('/images/barqapp_placeholder.jpg');
    }

    public function getPlaceholderImageUrlAttribute()
    {
        // Return the related media image or null if not available
        return $this->placeholder_image ? env('CRON_URL').'storage/'.$this->placeholder_image : url('/images/barqapp_placeholder_large.jpg');
    }

    public function getFollowersAttribute()
    {
        return $this->users()->count();
    }

    protected static function booted()
    {
        static::addGlobalScope(new SourceFilterScope);
    }


    /**
     * Get the list of tags attached to the article.
     *
     * @return array
     */
    public function news(){
        return $this->hasMany(News::class, 'source_id', 'id');
     }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_source','source_id','country_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_source','source_id','category_id');
    }

    public function users()
    {
        return $this->belongsToMany(Advertiser::class, 'user_sources', 'source_id', 'user_id');
    }

    public function sourcefeeds()
    {
        return $this->hasMany(SourceFeed::class,'source_id','id');
    }

    public function social_links()
    {
        return $this->hasMany(SocialMedia::class, 'source_id', 'id');
    }
}
