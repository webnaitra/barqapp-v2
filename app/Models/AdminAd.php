<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAd extends Model
{
    use HasFactory;
    protected $appends = ['image_url', 'view_url'];

    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'admin_ad_category', 'admin_ad_id', 'category_id');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'admin_ad_country','admin_ad_id','country_id');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'admin_ad_location', 'admin_ad_id', 'location_id');
    }

    public function getImageUrlAttribute()
    {
            $path = str_replace('public/', 'storage/', $this->image);
            return asset($path);
    }

    public function getViewUrlAttribute()
    {
        $id = base64_encode($this->id);
        return route('ads_share_view', array('id' => $id));
    }
    

}
