<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aiword extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'words', 'category_id',
    ];

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function tags(){
        return $this->belongsToMany(Tag::class, 'aiwords_tags', 'aiword_id', 'tag_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

}
