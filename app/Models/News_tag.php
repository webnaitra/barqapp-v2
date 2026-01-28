<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News_tag extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $fillable = [
        'news_id','tag_id'
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
