<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\AffiliateFilterScope;

class Affiliate extends Model
{
    use HasFactory;
    protected $table = 'affiliates';
    protected $appends = ['image_url'];
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new AffiliateFilterScope);
    }

    public function media(){
        return $this->hasOne(Media::class, 'id', 'image');
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'affiliate_categories', 'affiliate_id', 'category_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
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

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'affiliate_product_category', 'affiliate_id', 'product_category_id');
    }
    
    
    public static function getRandomAffiliates($limit = 4)
    {
        return self::with('media')
            ->select('id', 'name', 'url', 'price', 'selling_price', 'image')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

}
