<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AdsArea;

class Adsense extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'location_id', 'category_id', 'type', 'is_mobile','type'
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
        
    public function location(){
        return $this->belongsTo(Location::class);
    }

        public function category(){
        return $this->belongsTo(Category::class);
    }

}
