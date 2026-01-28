<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsArchieve extends Model
{
    use HasFactory;
    protected $table = 'news_archieves';
    protected $fillable = [
        'id','news_title','db_name'
    ];
}
