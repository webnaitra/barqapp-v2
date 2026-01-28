<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdminAd;

class Location extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'arabic_name' 
    ];


    public function ads()
    {
        return $this->belongsToMany(AdminAd::class, 'admin_ad_location', 'location_id', 'admin_ad_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class);
    }
}
