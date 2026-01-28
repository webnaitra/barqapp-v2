<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProductCategory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'product_product_category';
    public $timestamps = false;
    
    protected $fillable = [
        'product_id','product_category_id'
    ];
    
    /**
     * The relations to eager load on every query.
     *
     * @var array
     */

    /**
     * Get the list of tags attached to the article.
     *
     * @return array
     */

}
