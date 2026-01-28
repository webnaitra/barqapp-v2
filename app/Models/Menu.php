<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug'
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

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_id');
    }

}
