<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
    protected $guarded = [];

    public function liveStreams()
    {
        return $this->belongsToMany(Country::class, 'country_live_stream','country_id','live_stream_id');
    }

    public function sources()
    {
        return $this->belongsToMany(Source::class, 'country_source','country_id','source_id');
    }

}
