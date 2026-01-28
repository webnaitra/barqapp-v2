<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryLivestream extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'country_live_stream';
    
    protected $fillable = [
        'category_id','live_stream_id'
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
