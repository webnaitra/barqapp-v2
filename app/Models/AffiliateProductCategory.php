<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateProductCategory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'affiliate_product_category';
    
    protected $fillable = [
        'affiliate_id','product_category_id'
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