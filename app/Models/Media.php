<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'title', 'alt','status','url','slug'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = false;

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
    protected $appends = array('image_url');

    public function getImageUrlAttribute()
    {
        if (filter_var($this->url, FILTER_VALIDATE_URL) === FALSE) {
            $url = str_replace('phplaravel-920759-3202760.cloudwaysapps.com', 'dashboard.barqapp.net',  $this->url);
            return url($url);
        }

        $url = str_replace('phplaravel-920759-3202760.cloudwaysapps.com', 'dashboard.barqapp.net',  $this->url);
        return $url;
    }
}
