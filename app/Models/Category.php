<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\News;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','arabic_name','slug', 'color', 'order', 'image', 'order', 'icon_class', 'freeze', 'featured'
    ];

    protected $appends = array('image_url');



    public function news()
    {
        return $this->hasMany(News::class, 'category_id')
            ->with(['category'])
            ->latest()
            ->select(
                'id',
                'name',
                'slug',
                'category_id',
                'source_id',
                'image',
                'date',
                'urgent',
                'video',
                'source_link',
                'created_at'
            );
    }

        public function getImageUrlAttribute()
    {
            $path = str_replace('public/', 'storage/', $this->image);
            return asset($path);
    }

    public function users()
    {
        return $this->belongsToMany(Advertiser::class, 'user_categories', 'id', 'user_id');
    }

    public function sources()
    {
        return $this->belongsToMany(Source::class, 'category_source','category_id','source_id');
    }

    public function affiliates()
    {
        return $this->belongsToMany(Affiliate::class, 'affiliate_categories', 'category_id', 'affiliate_id');
    }

    public function ads()
    {
        return $this->belongsToMany(AdminAd::class, 'admin_ad_category', 'category_id', 'admin_ad_id');
    }

    public function keywords()
    {
        return $this->hasMany(Keyword::class, 'category_id', 'id');
    }
}
