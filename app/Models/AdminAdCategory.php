<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAdCategory extends Model
{
    use HasFactory;

    
    protected $table = 'admin_ad_category';
    
    protected $fillable = [
        'admin_ad_id','category_id'
    ];

}
