<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['image_url'];

    public $timestamps = false;

    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_category', 'product_category_id', 'product_id');
    }

    public function affiliates()
    {
        return $this->belongsToMany(Affiliate::class, 'affiliate_product_category', 'product_category_id', 'affiliate_id');
    }

    public function getImageUrlAttribute()
    {
            $path = str_replace('public/', 'storage/', $this->image);
            return asset($path);
    }
}
