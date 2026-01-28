<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification_setting extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_user_id', 'setting_cats', 'setting_sub_cats', 'setting_urgent'
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
