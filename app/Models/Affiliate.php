<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\AffiliateFilterScope;

class Affiliate extends Model
{
    use HasFactory;
    protected $table = 'affiliates';
    protected $appends = ['image_url', 'view_url'];
    protected $guarded = [];

    protected static function booted()
    {
        static::addGlobalScope(new AffiliateFilterScope);
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
            $path = str_replace('public/', 'storage/', $this->image);
            return asset($path);
    }

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'affiliate_product_category', 'affiliate_id', 'product_category_id');
    }

    /**
     * Append a custom attribute to the model source that will return the source url
     */
    public function getViewUrlAttribute()
    {
        $id = base64_encode($this->id);
        return route('affiliate_share_view', array('id' => $id));
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
