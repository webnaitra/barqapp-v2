<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contact_name', 'contact_email', 'contact_message'
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
