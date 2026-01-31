<?php

use App\Models\{
    User,
    Category,
    Location,
    Advertiser,
    Source,
    Tag,
    Keyword,
    News,
    ProductCategory,
    Country
};

//upload image
function generateRandomInteger($length = 5)
{
    $characters = '123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getYoutubeId($url)
{
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
    $youtube_id = (!empty($match[1]))?$match[1]:'';
    // dd($match[1]);
    return $youtube_id;
}

function getNewsTags($id)
{
    $tags = News::find($id)
    ->tags() 
    ->pluck('tags.id');
    return $tags->toArray();
}

function getNewsKeywords($id)
{
    $keywords = News::find($id)
    ->keywords() 
    ->pluck('keywords.id');
    return $keywords->toArray();
}

function getProductCategory()
{
    $productCategory = ProductCategory::all();
    return $productCategory;
}

/*
function getProductCat($id)
{
    // Product model is missing
    // $productCategory = ProductProductCategory::where('product_id', $id)->pluck('product_category_id');
    // return $productCategory->toArray();
    return [];
}
*/

function getAffiliateCat($id)
{
    $affiliate = \App\Models\Affiliate::find($id);
    return $affiliate ? $affiliate->productCategories()->pluck('product_categories.id')->toArray() : [];
}

/*
function getVideoCategoryIdsByVideoId($id)
{
    // VideoCategory model is missing
    // $videoCategoryIds  = VideoVideoCategory::where('video_id', $id)->pluck('video_category_id');
    // return $videoCategoryIds ->toArray();
    return [];
}

function getAllVideoCategories()
{
    // VideoCategory model is missing
    // $videoCategories  = VideoCategory::all();
    // return $videoCategories;
    return [];
}
*/

function getAffiliateNewsCat($id)
{
    $affiliate = \App\Models\Affiliate::find($id);
    return $affiliate ? $affiliate->categories()->pluck('categories.id')->toArray() : [];
}

function getAdNewsCat($id)
{
    $adminAd = \App\Models\AdminAd::find($id);
    return $adminAd ? $adminAd->categories()->pluck('categories.id')->toArray() : [];
}

function getliveStreamCountry($id)
{
    $liveStream = \App\Models\LiveStream::find($id);
    return $liveStream ? $liveStream->countries()->pluck('countries.id')->toArray() : [];
}

function getAllCountry()
{
    $country = Country::all();
    return $country;

}

function getNewsTagsWithName($id)
{
    $tags = News::find($id)
    ->tags() 
    ->pluck('tags.id', 'tags.tag_name');

    return $tags->toArray();
}


function convert($string)
{
    return strtr($string, array('۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9', '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9'));

}

function unreadNotificationsCount()
{
    $id = Auth::guard('advertiser')->user()->id;
    $count = Notification::where('notify_user_id', $id)
        ->where('notify_read', 0)
        ->count();
    return $count;
}










function sendSmsNotificaition($msg, $mobile)
{
    $api_url = 'https://www.hisms.ws/api.php?send_sms&username=966558806565&password=966558806565&numbers=' . $mobile . '&sender=URate&message=' . $msg . '&date=' . date('Y-m-d') . '&time=' . date("h:i");

    $response = Http::get($api_url);
    // dd($response);
    return $response;

}





function getLangObjectName($ar, $en)
{
    $name = (App::getLocale() == 'ar') ? $ar : ((!empty($en)) ? $en : $ar);
    return $name;
}


function userId()
{
    if (Auth::guard('advertiser')->user()->adv_panel_id == 0) {

        return Auth::guard('advertiser')->user()->id;
    } else {
        return Auth::guard('advertiser')->user()->adv_panel_id;

    }
}












//return default image
function defaultImage()
{
    return URL::to('/') . '/' . 'public/images/default.jpg';
}

//return image from media
function _img($id, $default = true)
{
    if (empty($id)) {
        return ($default) ? defaultImage() : "";
    }
    $media = Media::find($id);
    if (empty($media->url)) {
        return ($default) ? defaultImage() : "";
    }
    // $exists = Storage::disk('local')->has($media->url);
    // if(!$exists)
    // {
    //   return defaultImage();
    // }
    return URL::to('/') . '/' . $media->url;
}

function getOptionValue($key)
{
    $option = Option::where("option_name", $key)->first();
    return (!empty($option->option_value)) ? $option->option_value : "";
}


function getNewSource($id = 0)
{
    $newsSources = NewsSource::get();
    return isset($newsSources) ? $newsSources : array();
}

function getCategories($id = 0, $field = "name")
{
    if ($id == 0) {
        $cities = Category::all();
        return $cities;
    } else {
        $city = Category::find($id);
        return (!empty($city))?$city->$field:'';
    }

}



