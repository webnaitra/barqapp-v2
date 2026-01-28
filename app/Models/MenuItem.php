<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'url', 'target'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

}
