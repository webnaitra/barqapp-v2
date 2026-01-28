<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment_report extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'report_comment_id', 'report_user_id', 'report_notes'
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
     public function user()
     {
         return $this->belongsTo(Advertiser::class,'comment_user','id');
     }
     public function brand()
     {
         return $this->belongsTo(Brand::class,'comment_brand','id');
     }
}
