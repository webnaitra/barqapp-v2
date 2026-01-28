<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
     'notify_text', 'notify_type', 'notify_item_id', 'notify_read', 'notify_url'
    ];

}
