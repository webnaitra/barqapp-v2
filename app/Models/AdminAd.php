<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAd extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'admin_ad_category', 'admin_ad_id', 'category_id');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'admin_ad_location', 'admin_ad_id', 'location_id');
    }
    

}
