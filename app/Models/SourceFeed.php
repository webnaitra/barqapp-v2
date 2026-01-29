<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Country;
use App\Models\SourceType;
use App\Models\Terms;
use App\Models\Source;


class SourceFeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source_id',
        'source_url',
        'category_id',
        'status_id',
        'freeze'
    ];

    /**
     * Search query in multiple whereOr
     */
    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('name', 'like', '%'.$query.'%');

    }

    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
        public function country()
    {
        return $this->belongsTo(Country::class);
    }
    

    

       

}
