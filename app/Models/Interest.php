<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'interest_user_id', 'interest_word', 'old_id','interest_created_at'
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
