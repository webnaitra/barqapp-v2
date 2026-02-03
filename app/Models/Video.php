<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\VideoFilterScope;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use HasFactory;
    protected $table = 'videos';
    protected $guarded = [];
    protected $fillable = [
        'name',
        'image',
        'video',
        'source_link',
        'view_count',
        'source_id',
        'category_id',
    ];
    protected $appends = array('image_url', 'has_source_logo', 'source_icon', 'source', 'view_url');

    public function getHasSourceLogoAttribute()
    {
         
        if (str_contains($this->news_image, 'cronapp.jawlate.com')|| str_contains($this->news_image, 'cronapp.barqapp.net')) { 
            return true;
        }
        return false;
    }

    protected static function booted(){
        static::addGlobalScope(new VideoFilterScope);
    }


    /**
     * Get all of the posts that are assigned this tag.
     */
    public function category(){
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function sources(){
        return $this->belongsTo(Source::class, 'source_id', 'id');
    }

    /**
     * Append a custom attribute to the model source that will return the source name
     */
    public function getSourceAttribute()
    {
        return $this->sources->arabic_name ?? null;
    }

    /**
     * Append a custom attribute to the model source that will return the source url
     */
    public function getViewUrlAttribute()
    {
        if (empty($this->id)) {
            return null;
        }
        $id = base64_encode($this->id);
        return route('video_share_view', array('id' => $id));
    }

    /**
     * Check if the news item is favorited by the authenticated user
     */
    public function getSourceIconAttribute()
    {
        if($this->sources && $this->sources->logo){
            $path = str_replace('public/', 'storage/', $this->sources->logo);
            return asset($path);
        }
        return url('/images/barqapp_placeholder.jpg');
    }

     
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_video','video_id','country_id');
    }

    public function getImageUrlAttribute()
    {
        if (filter_var($this->image, FILTER_VALIDATE_URL) === FALSE) {
            $path = str_replace('public/', 'storage/', $this->image);
            return asset($path);
        }

        if (strpos($this->image, 'skynewsarabia.com') !== false) {
            $needle = "/95/53";
            $replacement = "/900/506";
            $url = str_replace($needle, $replacement, $this->image);
        } else {
            $url = $this->image;
        }
        return $url;
    }

        public function getImageAttribute($value)
    {
        if($value) {
            return $value;
        }
        if(!$this->sources) {
            return url('/images/barqapp_placeholder_large.jpg');
        }
        $source_image = $this->sources->placeholder_image ? $this->sources->placeholder_image : url('/images/barqapp_placeholder_large.jpg');
        return  $source_image;
    }

}
