<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_notification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_notify_user_id', 'user_notify_text', 'user_notify_suppose_count', 'user_notify_count'
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
     public function advertiser()
     {
         return $this->belongsTo(Advertiser::class,'notify_user_id','id');
     }


}
