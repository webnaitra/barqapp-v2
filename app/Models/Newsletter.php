<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'newsletter_email', 'newsletter_user_id'
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
