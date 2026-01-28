<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveStream extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_live_stream','live_stream_id','country_id');
    }
}
