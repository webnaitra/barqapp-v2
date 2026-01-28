<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function media(){
        return $this->hasOne(Media::class, 'id', 'image');
    }
    
    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category', 'product_id', 'product_category_id');
    }

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category', 'product_id', 'product_category_id');
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
