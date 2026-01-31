<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    protected $appends = array('image_url');
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tag_name', 'image'
    ];

    public function getImageUrlAttribute()
    {
            $path = str_replace('public/', 'storage/', $this->image);
            return asset($path);
    }

}
