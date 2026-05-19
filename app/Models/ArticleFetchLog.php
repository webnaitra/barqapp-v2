<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleFetchLog extends Model
{
    protected $fillable = [
        'batch_id',
        'source_name',
        'feed_url',
        'title',
        'link',
        'status',
    ];
}
