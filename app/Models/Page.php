<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $appends = array('image_url');
    protected $fillable = [
        'page_name', 'page_content', 'image', 'page_slug'
    ];

        public function getImageUrlAttribute()
    {
            $path = str_replace('public/', 'storage/', $this->image);
            return asset($path);
    }

}
