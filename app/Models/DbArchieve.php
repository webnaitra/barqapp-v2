<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbArchieve extends Model
{
    use HasFactory;

    protected $fillable = [
        'db_name'
    ];
}
