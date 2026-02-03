<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\AdvertiserResource;
use Illuminate\Http\Request;
use App\Http\Requests\Api\RegisterRequest;
use Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    Interest,
    News,
    Category,
    Adsense,
    Advertiser,
    Page,
    Notification_setting,
    Contact,
    Tag,
    Whatsapp,
    Newsletter,
    Favorite,
    News_tag,
    Menu,
    AdsArea

};
use Illuminate\Support\Facades\Hash;
use DB;
use Illuminate\Support\Facades\App;
use App\Mail\ConfirmationCodeMail;
use Mail;

class ApiController extends Controller
{
    public $table_prefix;
    public $meta_fields = array();

    public function __construct(Request $request)
    {
        // DB::select("ALTER TABLE advertisers ADD adv_player_ids TEXT NULL");
        $this->app_lang = (!empty($request->app_lang)) ? $request->app_lang : "ar";
        App::setLocale($this->app_lang);
    }

    private function missingParameter()
    {
        $response = array("status" => 404, "sub_message" => "failure", "return" => array(), "message" => "Missing Parameters");
        return response($response);
    }

    private function returnResponse($code, $sub, $return, $message)
    {
        $response = array("status" => $code, "sub_message" => $sub, "return" => $return, "message" => $message);
        return response($response);
    }

    private function removeMeta($objects, $single = false)
    {
        if ($single) {
            $myObject = $objects;
            $objects = array();
            $objects[] = $myObject;
        }
        $newObjects = array();
        foreach ($objects as $object) {
            foreach ($object as $key => $value) {
                if (in_array($key, $this->meta_fields)) {
                    unset($object[$key]);
                }
                $cityId = $this->table_prefix . "city_id";
                $catId = $this->table_prefix . "cat_id";
                $subCatId = $this->table_prefix . "sub_cat_id";
                $otherImages = $this->table_prefix . "other_images";
                if ($key == $cityId) {
                    if (!empty($value)) {
                        $object['city_name'] = ($this->app_lang == "ar") ? getCities($value, 'city_name') : getCities($value, 'city_name_en');
                    } else {
                        $object['city_name'] = "";
                    }
                }
                if ($key == 'created_at') {
                    if (!empty($value)) {
                        $object['created_at'] = date('d/m/Y', strtotime($value));
                    } else {
                        $object['created_at'] = "";
                    }
                }
                if ($key == $catId) {
                    if (!empty($value)) {
                        $object[$this->table_prefix . 'name'] = ($this->app_lang == "ar") ? getCategories($value, 'name') : getCategories($value, 'name_en');
                    } else {
                        $object[$this->table_prefix . 'name'] = "";
                    }
                }
                if ($key == $subCatId) {
                    if (!empty($value)) {
                        $object[$this->table_prefix . 'sub_name'] = ($this->app_lang == "ar") ? getCategories($value, 'name') : getCategories($value, 'name_en');
                    } else {
                        $object[$this->table_prefix . 'sub_name'] = "";
                    }
                }
                if ($key == "image") {
                    if (!empty($value)) {
                        $object['imageUrl'] = _img($value);
                    } else {
                        $object['imageUrl'] = "";
                    }
                }
                if ($key == "image_v") {
                    if (!empty($value)) {
                        $object['imageUrlHorizontal'] = _img($value);
                    } else {
                        $object['imageUrlHorizontal'] = "";
                    }
                }
                if ($key == $otherImages) {
                    $images = unserialize($value);
                    if (!empty($images)) {
                        $imagesArray = array();
                        foreach ($images as $image) {
                            if (!empty($image)) {
                                $imagesArray[] = _img($image);
                            }
                        }
                        $object['images'] = $imagesArray;
                    } else {
                        $object['images'] = array();
                        $object['images'][] = asset('/images/Bitmap@4x.png');
                    }
                }
            }
            $object = $this->checkDataTypes($object);
            $newObjects[] = $object;
        }
        return ($single) ? $newObjects[0] : $newObjects;
    }

    private function checkDataTypes($object)
    {
        foreach ($object as $key => $value) {
            if (is_string($value)) {
                $value = strval($value);
            } elseif (is_numeric($value) && !in_array($key, array("is_open", "ratesNumber", "brand_avg_rate"))) {
                $value = strval($value);
            } elseif ($value === null) {
                $value = strval($value);
            } else {
                $value = $value;
            }
            if ($key == "rate_user") {
                $object["rate_user_id"] = $value;
            }
            if ($key == "rate_branch") {
                $object["rate_branch_id"] = $value;
            }
            $object[$key] = $value;
        }
        return $object;
    }

    public function getPage(Request $request)
    {
        $slug = $request->slug;
        $terms = Page::where('page_slug', $slug)->first();
        $terms->imageUrl = _img($terms->image);
        return $this->returnResponse(200, 'success', $terms, 'found');
    }

    public function signup(RegisterRequest $request)
    {
        $password = Hash::make($request->password);
        $request->merge([
            'password' => $password,
        ]);
        $username = $request->firstName . ' ' . $request->lastName;
        $advertiser = Advertiser::updateOrCreate([
            'adv_first_name' => $request->firstName,
            'adv_last_name' => $request->lastName,
            'adv_username' => $username,
            'adv_email' => $request->email,
            'adv_password' => $password,
            'adv_login_type' => $request->loginType
        ]);
        if (!empty($_FILES['image'])) {
            $imageId = upload($request, 'image', 'image');
            if (!empty($imageId)) {
                $advertiser->image = $imageId;
            }
        }
        $advertiser->save();
        $token = $advertiser->createToken('Laravel Password Grant Client')->accessToken;
        return $this->returnResponse(200, 'success', ['data' =>
            [
                'advertiser' => new AdvertiserResource(($advertiser)),
                'token' => $token
            ]], 'Registered');
    }

    public function login(Request $request)
    {
        $password = $request->password;
        $credentials = [
            'email' => $request['email'],
            'password' => $request['password'],
        ];
        try {
            $advertiser = Advertiser::where('adv_email', $request->email)
                ->first();
            if (empty($advertiser->id)) {
                return response()->json(['status' => 400, 'message' => __('messages.wrong_email')]);
            }
            if (!Hash::check($password, $advertiser->adv_password)) {
                return response()->json(['status' => 400, 'message' => __('messages.wrong_password')]);
            }
            $token = $advertiser->createToken('Laravel Password Grant Client')->accessToken;
            return $this->returnResponse(200, 'success', ['data' =>
                [
                    'advertiser' => new AdvertiserResource(($advertiser)),
                    'token' => $token
                ]], 'Logined');

        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    public function forgotPassword(Request $request)
    {
        $request = $request->all();
        $email = (!empty($request['email'])) ? $request['email'] : '';
        if (empty($email)) {
            return $this->missingParameter();
        }
        $advertiser = Advertiser::where('adv_email', $email)->first();
        if (empty($advertiser)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $advertiser->adv_forgot_password_code = generateRandomInteger(30);
        $advertiser->save();
        //Mail::to($email)->send(new ConfirmationCodeMail($advertiser->adv_forgot_password_code));
        $message = ($this->app_lang == "ar") ? "رمز تغيير كلمة المرور" : "Change password code";
        return $this->returnResponse(200, 'success', [
            'code' => strval($advertiser->adv_forgot_password_code)
        ], $message);
    }

    public function resetPassword(Request $request)
    {
        $request = $request->all();
        $code = (!empty($request['code'])) ? $request['code'] : '';
        $password = (!empty($request['password'])) ? $request['password'] : '';
        if (empty($code)) {
            return $this->missingParameter();
        }
        $advertiser = Advertiser::where('adv_forgot_password_code', $code)->first();
        if (empty($advertiser)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        if (strlen($password) < 8) {
            $message = ($this->app_lang == "ar") ? " كلمة المرور يجب الا تقل عن 8 خانات" : "Email is already used";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $advertiser->adv_forgot_password_code = NULL;
        $advertiser->adv_password = Hash::make($password);
        $advertiser->save();
        return $this->returnResponse(200, 'success', 1, 'تم تعديل كلمة المرور بنجاح');
    }

    public function getUserData(Request $request)
    {
        $advertiser = Advertiser::find($request->userId);
        if (!empty($advertiser)) {
            $this->table_prefix = "adv_";
            return $this->returnResponse(200, 'success', new AdvertiserResource($advertiser), 'Found');
        }
        $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
        return $this->returnResponse(403, 'failure', 0, $message);
    }

    public function getTags(Request $request)
    {
        $tags = Tag::select("id as tag_id", 'tag_name', 'image')
            ->orderBy("id", 'desc')->get();
        $this->table_prefix = "tag_";
        $tags = $this->removeMeta($tags->toArray());
        return $this->returnResponse(200, 'success', $tags, 'found');

    }

    public function homepage_tags(){

        return  Tag::select("id as tag_id", 'tag_name', 'image')
                 ->orderBy("id", 'desc')->take(10)->get();
    }

    public function getMainCategories(Request $request)
    {
        $catId = $request->catId;
        $categories = Category::select("id as cat_id", 'name', 'order','slug','color')->orderBy("order", 'asc');
        if (!empty($catId)) {
            $categories = $categories->where('id', $catId);
        }
        $categories = $categories->get();
        $this->table_prefix = "cat_";
        $categories = $this->removeMeta($categories->toArray());
        return $this->returnResponse(200, 'success', $categories, 'found');
    }

    public function HomePageCategory(){

        $categories = Category::select("id as cat_id", 'name', 'order','slug','color')
            ->where("order",'!=', 'null')->orderBy("order", 'asc');
        if (!empty($catId)) {
            $categories = $categories->where('id', $catId);
        }
        $categories = $categories->get();
        $this->table_prefix = "cat_";
        $categories = $this->removeMeta($categories->toArray());
        return $this->returnResponse(200, 'success', $categories, 'found');

    }

    public function getCategoriesWithNews()
    {
        $categories = Category::select("id as cat_id", 'name', 'order')->orderBy("order", 'asc');
        $categories = $categories->get();
        $cats = array();
        foreach ($categories as $category) {
            $cat = $this->getCategoryNews($category);
            if (!empty($cat['news'])) $cats[] = $cat;
        }
        // $this->table_prefix = "cat_";
        // $categories = $this->removeMeta($categories->toArray());
        return $cats;
    }

    public function getCategoryNews($category)
    {
        $news = News::select("id", 'name', 'image', 'category_id',
             'urgent', 'video', 'source_id', 'source_link', 'created_at'
        )->where('category_id', $category->cat_id)->limit(3)->get();
        foreach ($news as $newsItem) {
            $newsItem->video = (!empty($newsItem->video)) ? 'https://www.youtube.com/embed/' . getYoutubeId($newsItem->video) : '';
            if (!empty($fav_user)) {
                $oldFavorite = Favorite::where('fav_news_id', $newsItem->id)
                    ->where('fav_user_id', $fav_user)->first();
                if (!empty($oldFavorite)) {
                    $newsItem->is_favorite = 1;
                } else {
                    $newsItem->is_favorite = 0;
                }
            } else {
                $newsItem->is_favorite = 0;
                $fav_user = '';
            }
            $newsItem->relatedNews = $this->getRelatedNews($newsItem, $fav_user);
            $newsItem->newsTags = getNewsTagsWithName($newsItem->id);
        }
        $this->table_prefix = "news_";
        $news = $this->removeMeta($news->toArray());
        $cat = array();
        $cat['name'] = $category->name;
        $cat['news'] = $news;
        return $cat;
    }
    


    public function getAdsense(Request $request)
    {
        $area = $request->area;
        $adsense = Adsense::select("id", 'adsense_name', 'adsense_code', 'adsense_area');
        if (isset($area)) {
            $adsense = $adsense->where('adsense_area', $area);
        }
        $adsense = $adsense->get();
        $this->table_prefix = "adsense_";
        $adsense = $this->removeMeta($adsense->toArray());
        return $this->returnResponse(200, 'success', $adsense, 'found');
    }

    public function getAllNews(Request $request){







    }

    public function getNews(Request $request)
    {
        $fav_user = $request->favUser;
        $cat = $request->cat;
        $newsId = $request->newsId;
        $subCat = $request->subCat;
        $source = $request->source;
        $tags = $request->tags;
        $keyword = $request->keyword;
        $mostViewed = $request->mostViewed;
        $favUserId = $request->favUserId;
        $news = News::select("id", 'name', 'content', 'image', 'category_id',
             'views', 'shares', 'urgent', 'video', 'source_id', 'source_link', 'created_at'
        );
        if (isset($newsId)) {
            $news = $news->where('id', $newsId);
        }
        if (isset($cat)) {
            $news = $news->where('category_id', $cat);
        }
        if (isset($keyword)) {
            $news = $news->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('content', 'LIKE', '%' . $keyword . '%')
                ->orWhereIn('category_id', function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                })->orWhereIn( function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                });
        }
        if (isset($subCat)) {
            $news = $news->where( $subCat);
        }
        if (isset($source)) {
            $news = $news->where('source_id', $source);
        }
        if (isset($tags)) {
            $newsTags = News_tag::whereIn('tag_id', $tags)->pluck('news_id');
            $news->whereIn('id', $newsTags);
        }
        if (isset($favUserId)) {
            $news = $news->whereIn('id', function ($query) use ($favUserId) {
                $query->select('fav_news_id')
                    ->from(with(new Favorite)->getTable())
                    ->where('fav_user_id', $favUserId)
                    ->distinct('fav_news_id')->pluck('fav_news_id')->toArray();
            });
        }
        if (isset($mostViewed)) {
            $news = $news->orderBy('news_views', 'Desc')->limit(3);
        } else {
            $news = $news->orderBy('id', 'Desc');
        }

        $news = $news->paginate(20);
        foreach ($news as $newsItem) {
            $newsItem->video = (!empty($newsItem->video)) ? 'https://www.youtube.com/embed/' . getYoutubeId($newsItem->video) : '';
            if (!empty($fav_user)) {
                $oldFavorite = Favorite::where('fav_news_id', $newsItem->id)
                    ->where('fav_user_id', $fav_user)->first();
                if (!empty($oldFavorite)) {
                    $newsItem->is_favorite = 1;
                } else {
                    $newsItem->is_favorite = 0;
                }
            } else {
                $newsItem->is_favorite = 0;
                $fav_user = '';
            }
            $newsItem->relatedNews = $this->getRelatedNews($newsItem, $fav_user);
            $newsItem->newsTags = getNewsTagsWithName($newsItem->id);
            $newsItem->previousNews = $this->getPreviousNews($newsItem, $fav_user);
            $newsItem->otherCat = $this->allnews_categories($newsItem);

        }
        $this->table_prefix = "news_";
//        $news = $this->removeMeta($news->toArray());
        return $this->returnResponse(200, 'success', $news, 'found');
    }

    public function SearchNews(Request $request){

        $keyword = $request->keyword;
        $news = [];
        if (!empty($keyword)) {

            $news = News::select("id", 'name', 'content', 'image', 'category_id',
                 'views', 'shares', 'urgent', 'video', 'source_id', 'source_link', 'created_at');

            $news = $news->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('content', 'LIKE', '%' . $keyword . '%')
                ->orWhereIn('category_id', function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                })->orWhereIn( function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                });
            $news = $news->paginate(20);
            return $this->returnResponse(200, 'success', $news, 'found');

        }else{

            return $this->returnResponse(403, 'failure', $news,'not found');

        }


    }

    public function homepage(){

        $home = [];
        $home['tags'] = $this->homepage_tags();
       return $this->returnResponse(200, 'success', $home, 'found');
    }
    
    public function getPreviousNews($newsItem, $fav_user=''){

        $fav_user = $fav_user;
        $news = News::select("id", 'name', 'image',
            'source_id', 'source_link', 'created_at'
        );
        $previous_id = $news->where('id', '<', $newsItem->id)->max('id');
        $news = $news->where('id','=',$previous_id)->first();

        return $news;


    }
    public function allnews_categories($newsItem){

        $news =  DB::table("news_categories")->where('news_id',$newsItem->id)->get();
        $categories = [];

        foreach($news as $news_name){

            $name = \App\Models\Category::where('id',$news_name->cat_id)->first();
            if(!empty($name)){
                array_push($categories,['cat_id'=>$name->id,'name'=>$name->name]);

            }
        }

        return   array_unique($categories,SORT_REGULAR);
    }
    public function getMobileNews(Request $request)
    {
        $fav_user = $request->favUser;
        $cat = $request->cat;
        $subCat = $request->subCat;
        $source = $request->source;
        $tags = $request->tags;
        $keyword = $request->keyword;
        $mostViewed = $request->mostViewed;
        $news = News::select("id", 'name', 'content', 'image', 'category_id',
             'views', 'shares', 'urgent', 'video', 'source_id', 'source_link', 'created_at'
        );
        if (isset($cat)) {
            $news = $news->where('category_id', $cat);
        }
        if (isset($keyword)) {
            $news = $news->where('name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('content', 'LIKE', '%' . $keyword . '%')
                ->orWhereIn('category_id', function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                })->orWhereIn( function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                });
        }
        if (isset($subCat)) {
            $news = $news->where( $subCat);
        }
        if (isset($source)) {
            $news = $news->where('news_source', $source);
        }
        if (isset($tags)) {
            $newsTags = News_tag::whereIn('tag_id', $tags)->pluck('news_id');
            $news->whereIn('id', $newsTags);
        }
        if (isset($mostViewed)) {
            $news = $news->orderBy('news_views', 'Desc')->limit(3);
        } else {
            $news = $news->orderBy('id', 'Desc');
        }
        $news = $news->limit(5);
        $news = $news->get();
        foreach ($news as $newsItem) {
            $newsItem->video = (!empty($newsItem->video)) ? 'https://www.youtube.com/embed/' . getYoutubeId($newsItem->video) : '';
            if (!empty($fav_user)) {
                $oldFavorite = Favorite::where('fav_news_id', $newsItem->id)
                    ->where('fav_user_id', $fav_user)->first();
                if (!empty($oldFavorite)) {
                    $newsItem->is_favorite = 1;
                } else {
                    $newsItem->is_favorite = 0;
                }
            } else {
                $newsItem->is_favorite = 0;
                $fav_user = '';
            }
            // $newsItem->relatedNews = $this->getRelatedNews($newsItem, $fav_user);
            // $newsItem->newsTags = getNewsTagsWithName($newsItem->id);
        }
        $categoriesNews = $this->getCategoriesWithNews();
        $this->table_prefix = "news_";
        $news = $this->removeMeta($news->toArray());
        // return $this->returnResponse(200,'success',$news,'found');
        $response = array("status" => 200, "sub_message" => 'success', "return" => $news, 'categories' => $categoriesNews, "message" => 'found');
        return response($response);
    }

    public function getRelatedNews($newsItem, $fav_user = '')
    {
        $fav_user = $fav_user;
        $news = News::select("id", 'name', 'image',
            'source_id', 'source_link', 'created_at'
        );
        $news = $news->where('category_id', $newsItem->category_id)
            ->where('id', '!=', $newsItem->id)
            ->get();
        foreach ($news as $newsItem) {
            if (!empty($fav_user)) {
                $oldFavorite = Favorite::where('fav_news_id', $newsItem->id)
                    ->where('fav_user_id', $fav_user)->first();
                if (!empty($oldFavorite)) {
                    $newsItem->is_favorite = 1;
                } else {
                    $newsItem->is_favorite = 1;
                }
            } else {
                $newsItem->is_favorite = 1;
            }
        }
        $this->table_prefix = "news_";
        $news = $this->removeMeta($news->toArray());
        return $news;
    }

    public function getFooterMenu()
    {
        $menus = Menu::select("id as menu_id", 'menu_name','menu_parent_slug', 'menu_content')
            ->orderBy("id", 'asc')->get();
        foreach ($menus as $menu) {
            $menu->menu_list = unserialize($menu->menu_content);
            $list = array();
            for ($i = 0; $i < count($menu->menu_list['names']); $i++) {
                $munuSingle = new \stdClass;
                $munuSingle->name = $menu->menu_list['names'][$i];
                $munuSingle->link = $menu->menu_list['links'][$i];
                $list[] = $munuSingle;
            }
            $menu->menu_list = $list;
        }
        $this->table_prefix = "menu_";
        $this->meta_fields[] = "menu_content";
        $menus = $this->removeMeta($menus->toArray());
        return $this->returnResponse(200, 'success', $menus, 'found');
    }

    public function getCities($ids = '')
    {
        $cities = City::select("id as city_id", 'city_name', 'city_name_en', 'city_map_location')->where('city_status', 1);
        if (isset($ids)) {
            $cities = $cities->whereIn('id', $ids)->get();
            //   dd($cities);
        } else {
            $cities = $cities->get();
        }
        foreach ($cities as $city) {
            $city->city_title = ($this->app_lang == "ar") ? $city->city_name : $city->city_name_en;
        }
        $this->table_prefix = "cat_";
        $this->meta_fields[] = "city_name";
        $this->meta_fields[] = "city_name_en";
        $cities = $this->removeMeta($cities->toArray());
        return $this->returnResponse(200, 'success', $cities, 'found');
    }

    public function addInterest(Request $request)
    {
        $request = $request->all();
        $userId = (!empty($request['userId'])) ? $request['userId'] : '';
        $word = (!empty($request['word'])) ? $request['word'] : '';
        $createdAt = (!empty($_POST['createdAt'])) ? $_POST['createdAt'] : '';
        if (empty($userId) || empty($word)) {
            return $this->missingParameter();
        }
        $interest = new Interest;
        $interest->interest_user_id = $userId;
        $interest->interest_word = $word;
        $interest->interest_created_at = $createdAt;
        $interest->save();
        $message = ($this->app_lang == "ar") ? " تمت الاضافة بنجاح" : "Inserted successfully";
        return $this->returnResponse(200, 'success', $createdAt, $message);
    }

    public function aboutApp()
    {
        return $this->get_option_value('app_about');
    }

    public function getSettings()
    {
        $array = array('app_facebook', 'app_twitter', 'app_whatsapp', 'app_massenger',
            'footer_logo', 'footer_text', 'newsletter_text', 'app_google_play', 'app_app_store', 'copyright', 'app_logo');
        $obj = new \stdClass;
        foreach ($array as $key) {
            $value = getOptionValue($key);
            if ($key == 'footer_logo')
                $obj->$key = _img($value);
            else
                $obj->$key = $value;
        }
        return $this->returnResponse(200, 'success', $obj, 'found');
    }

    public function appIntroduction()
    {
        return $this->get_option_value('app_intro');
    }

    public function appTerms()
    {
        return $this->get_option_value('app_terms');
    }

    public function appPrivacy()
    {
        return $this->get_option_value('app_privacy');
    }

    public function get_option_value($key)
    {
        $key = ($this->app_lang == "ar") ? $key : $key . "_en";
        $value = getOptionValue($key);
        return $this->returnResponse(200, 'success', $value, 'found');
    }

    public function saveContactUs(Request $request)
    {
        $request = $request->all();
        $name = (!empty($request['name'])) ? $request['name'] : '';
        $email = (!empty($request['email'])) ? $request['email'] : '';
        $message = (!empty($request['message'])) ? $request['message'] : '';
        if (empty($name) || empty($email) || empty($message)) {
            return $this->missingParameter();
        }
        $contact = new Contact;
        $contact->contact_name = $name;
        $contact->contact_email = $email;
        $contact->contact_message = $message;
        $contact->save();
        $message = ($this->app_lang == "ar") ? " تمت الاضافة بنجاح" : "Inserted successfully";
//        addAdminNotify('new_contact', $contact->id, route('contacts_preview', $contact->id));

        return $this->returnResponse(200, 'success', 1, $message);
    }
    
    public function ads_areas(Request $request){
        
        
    if(isset($request->news_id)){
        $obj = [];
        $adsence =Adsense::where('id',$request->news_id)->get();
        foreach($adsence as $key =>$value){

            $ads_areas = AdsArea::where('id', $value['adsense_area'])->first();
            
            $obj['adsense_name'] =$value['adsense_name'];
            $obj['adsense_area'] =$ads_areas->name;
            $obj['adsense_code'] =$value['adsense_code'];
            
        }        
            
      return $this->returnResponse(200, 'success', $obj, 'found');

    }else{
      return $this->returnResponse(404, 'success', 1, 'Not found');

    }           


    }

    public function getInterests(Request $request)
    {
        $request = $request->all();
        $userId = (!empty($request['userId'])) ? $request['userId'] : '';
        if (empty($userId)) {
            return $this->missingParameter();
        }
        $interests = Interest::select("id as interest_id", 'interest_word', 'interest_user_id', 'interest_created_at')
            ->where('interest_user_id', $userId)
            ->get();
        $interests = $this->removeMeta($interests->toArray());
        return $this->returnResponse(200, 'success', $interests, 'found');

    }

    public function deleteInterest(Request $request)
    {
        $request = $request->all();
        $word = (!empty($request['word'])) ? $request['word'] : '';
        if (empty($word)) {
            return $this->missingParameter();
        }
        $interest = Interest::where("interest_word", $word)->get();
        if (count($interest) > 0) {
            $interest->each->delete();
            $message = ($this->app_lang == "ar") ? "   تم الحذف بنجاح" : "Deleted successfully";
        } else {
            $message = ($this->app_lang == "ar") ? "السجل غير موجود" : "Record not found";
        }
        return $this->returnResponse(200, 'success', 1, $message);
    }




    public function addUserFavorite()
    {
		//start here
        $favUserId = (!empty($_POST['favUserId'])) ? $_POST['favUserId'] : '';
        $newsId = (!empty($_POST['newsId'])) ? $_POST['newsId'] : '';
        if (empty($favUserId) || empty($newsId)) {
            return $this->missingParameter();
        }
        $user = Advertiser::find($favUserId);
        if (empty($user)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $newsItem = News::find($newsId);
        if (empty($newsItem)) {
            $message = ($this->app_lang == "ar") ? " الخبر غير موجود" : "News does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $oldFavorite = Favorite::where('fav_news_id', $newsId)
            ->where('fav_user_id', $favUserId)->first();;
        if (!empty($oldFavorite)) {
            $message = ($this->app_lang == "ar") ? "قام المستخدم باضافة الخبر في مفضلته من قبل" : "The user has this news in his favorites";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $favorite = new Favorite;
        $favorite->fav_user_id = $favUserId;
        $favorite->fav_news_id = $newsId;
        $favorite->save();
        $message = ($this->app_lang == "ar") ? " تمت الاضافة بنجاح" : "Inserted successfully";

        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function updateNotificationSettings()
    {
        $userId = (!empty($_POST['userId'])) ? $_POST['userId'] : '';
        $catsId = (!empty($_POST['catsId'])) ? $_POST['catsId'] : '';
        $subsId = (!empty($_POST['subsId'])) ? $_POST['subsId'] : '';
        $urgent = (!empty($_POST['urgent'])) ? $_POST['urgent'] : '';
        if (empty($userId)) {
            return $this->missingParameter();
        }
        $setting = Notification_setting::where('setting_user_id', $userId)->first();
        if (!empty($setting)) {
            $setting = $setting;
        } else {
            $setting = new Notification_setting;
        }
        if (!empty($userId))
            $setting->setting_user_id = $userId;
        if (!empty($catsId))
            $setting->setting_cats = $catsId;
        if (!empty($subsId))
            $setting->setting_sub_cats = $subsId;
        if (!empty($urgent) || $urgent === 0)
            $setting->setting_urgent = intval($urgent);

        $setting->save();
        $message = ($this->app_lang == "ar") ? " تمت الاضافة بنجاح" : "Inserted successfully";

        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function getNotificationSettings(Request $request)
    {
        $userId = (!empty($request->userId)) ? $request->userId : '';
        if (empty($userId)) {
            return $this->missingParameter();
        }
        $setting = Notification_setting::select('setting_cats', 'setting_sub_cats', 'setting_urgent')
            ->where('setting_user_id', $userId)->first();
        if (!empty($setting)) {

            $setting->setting_cats = (!empty($setting)) ? explode("_", $setting->setting_cats) : array();
            $setting->setting_sub_cats = (!empty($setting)) ? explode("_", $setting->setting_sub_cats) : array();
            $setting->setting_urgent = (!empty($setting)) ? $setting->setting_urgent : 0;
        } else {
            $setting = new \stdClass();
        }
        return $this->returnResponse(200, 'success', $setting, 'found');
    }

    public function subscribeToNewsletter()
    {
        $email = (!empty($_POST['email'])) ? $_POST['email'] : '';
        $userId = (!empty($_POST['userId'])) ? $_POST['userId'] : '';
        if (empty($email)) {
            return $this->missingParameter();
        }
        if (!empty($userId)) {
            $user = Advertiser::find($userId);
            if (empty($user)) {
                $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
                return $this->returnResponse(403, 'failure', 0, $message);
            }
        }
        $oldNewsletter = Newsletter::where('newsletter_email', $email)->first();
        if (!empty($oldNewsletter)) {
            $message = ($this->app_lang == "ar") ? "هذا البريد مسجل في القائمة البريدية من قبل" : "The email is already subscribed";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $newsletter = new Newsletter;
        $newsletter->newsletter_email = $email;
        $newsletter->newsletter_user_id = (!empty($userId)) ? $userId : 0;
        $newsletter->save();
        $message = ($this->app_lang == "ar") ? " تم الاشتراك بنجاح" : "You have been subscribed successfully";
        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function subscribeToWhatsapp()
    {
        $mobile = (!empty($_POST['mobile'])) ? $_POST['mobile'] : '';
        $userId = (!empty($_POST['userId'])) ? $_POST['userId'] : '';
        if (empty($mobile)) {
            return $this->missingParameter();
        }
        if (!empty($userId)) {
            $user = Advertiser::find($userId);
            if (empty($user)) {
                $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
                return $this->returnResponse(403, 'failure', 0, $message);
            }
        }
        $oldWhatsapp = Whatsapp::where('whatsapp_number', $mobile)->first();
        if (!empty($oldWhatsapp)) {
            $message = ($this->app_lang == "ar") ? " هذا الرقم مسجل من قبل" : "The phone number is already subscribed";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $whatsapp = new Whatsapp;
        $whatsapp->whatsapp_number = $mobile;
        $whatsapp->whatsapp_user_id = (!empty($userId)) ? $userId : 0;
        $whatsapp->save();
        $message = ($this->app_lang == "ar") ? " تم الاشتراك بنجاح" : "You have been subscribed successfully";
        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function increaseViews()
    {
        $newsId = (!empty($_POST['newsId'])) ? $_POST['newsId'] : '';
        if (empty($newsId)) {
            return $this->missingParameter();
        }

        $newsItem = News::find($newsId);
        if (empty($newsItem)) {
            $message = ($this->app_lang == "ar") ? " الخبر غير موجود" : "News does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $views = $newsItem->news_views;
        $views++;
        $newsItem->news_views = $views;
        $newsItem->save();
        $message = ($this->app_lang == "ar") ? " تمت الاضافة بنجاح" : "Inserted successfully";
        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function increaseShares()
    {
        $newsId = (!empty($_POST['newsId'])) ? $_POST['newsId'] : '';
        if (empty($newsId)) {
            return $this->missingParameter();
        }

        $newsItem = News::find($newsId);
        if (empty($newsItem)) {
            $message = ($this->app_lang == "ar") ? " الخبر غير موجود" : "News does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $shares = $newsItem->news_shares;
        $shares++;
        $newsItem->news_shares = $shares;
        $newsItem->save();
        $message = ($this->app_lang == "ar") ? " تمت الاضافة بنجاح" : "Inserted successfully";
        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function getUserNotificationsCount()
    {
        $userId = (!empty($_POST['userId'])) ? $_POST['userId'] : '';
        if (empty($userId)) {
            return $this->missingParameter();
        }
        $notifications = Notification::where('notify_user_id', $userId)
            ->where('notify_read', 0)
            ->count();
        return $this->returnResponse(200, 'success', $notifications, 'found');

    }



    public function deleteUserFavorite()
    {
        $favUserId = (!empty($_POST['favUserId'])) ? $_POST['favUserId'] : '';
        $newsId = (!empty($_POST['newsId'])) ? $_POST['newsId'] : '';
        if (empty($favUserId) || empty($newsId)) {
            return $this->missingParameter();
        }
        $user = Advertiser::find($favUserId);
        if (empty($user)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $news = News::find($newsId);
        if (empty($news)) {
            $message = ($this->app_lang == "ar") ? " الخبر غير موجود" : "News does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $oldFavorite = Favorite::where('fav_news_id', $newsId)
            ->where('fav_user_id', $favUserId)->first();;
        if (empty($oldFavorite)) {
            $message = ($this->app_lang == "ar") ? " الخبر غير مضاف للمفضلة" : "The user does not have this brand in his favorites";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        Favorite::where('fav_news_id', $newsId)
            ->where('fav_user_id', $favUserId)->delete();
        $message = ($this->app_lang == "ar") ? "   تم الحذف بنجاح" : "Deleted successfully";

        return $this->returnResponse(200, 'success', 1, $message);

    }



    public function updateProfile(Request $request)
    {
        $userId = (!empty($_POST['userId'])) ? $_POST['userId'] : '';
        $first_name = (!empty($_POST['first_name'])) ? $_POST['first_name'] : '';
        $last_name = (!empty($_POST['last_name'])) ? $_POST['last_name'] : '';
        $mobile = (!empty($_POST['mobile'])) ? $_POST['mobile'] : '';
        $email = (!empty($_POST['email'])) ? $_POST['email'] : '';
        $age = (!empty($_POST['age'])) ? $_POST['age'] : '';
        $cityName = (!empty($_POST['city'])) ? $_POST['city'] : '';
        if (empty($userId)) {
            return $this->missingParameter();
        }
        $user = Advertiser::find($userId);
        if (empty($user)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $advertiserOld = Advertiser::where('adv_mobile', $mobile)->first();
        if (!empty($advertiserOld) && $advertiserOld->id != $userId) {
            $message = ($this->app_lang == "ar") ? "رقم الجوال مستخدم من قبل" : "Mobile number is already used";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $advertiser = Advertiser::find($userId);
        if (!empty($first_name)) {
            $advertiser->adv_first_name = $first_name;
        }
        if (!empty($last_name)) {
            $advertiser->adv_last_name = $last_name;
        }
        if (!empty($email)) {
            $advertiser->adv_email = $email;
        }
        if (!empty($age)) {
            $advertiser->adv_age = $age;
        }
        if (!empty($mobile)) {
            $advertiser->adv_mobile = $mobile;
        }
        if (!empty($_FILES['imageData'])) {
            $imageId = upload($request, 'imageData', 'image');
            if (!empty($imageId)) {
                if ($advertiser->adv_type == 'user') {
                    $advertiser->image = $imageId;
                }
            }
        }
        $advertiser->update();
        $this->table_prefix = "adv_";
        $advertiser->adv_id = $advertiser->id;
        $advertiser = $this->removeMeta($advertiser->toArray(), true);
        $array = array($advertiser);
        $message = ($this->app_lang == "ar") ? "   تم التعديل بنجاح" : "Updated successfully";

        return $this->returnResponse(200, 'success', $array, $message);
    }

    public function getUserFavorites(Request $request)
    {
        $favUserId = (!empty($request->favUserId)) ? $request->favUserId : '';
        return $this->getNews($request);
    }

    public function changeLang()
    {
        $lang = (!empty($_POST['lang'])) ? $_POST['lang'] : "";
        $userId = (!empty($_POST['userId'])) ? $_POST['userId'] : "";
        if (empty($lang) || empty($userId)) {
            return $this->missingParameter();
        }
        $user = Advertiser::find($userId);
        if (empty($user)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        $user->adv_lang = $lang;
        $user->save();
        $message = ($this->app_lang == "ar") ? "   تم التعديل بنجاح" : "Updated successfully";
        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function getSocialLinks()
    {
        $facebook = getOptionValue('app_facebook') ?? "";
        $twitter = getOptionValue('app_twitter') ?? "";
        $whatsapp = getOptionValue('app_whatsapp') ?? "";
        $massenger = getOptionValue('app_massenger') ?? "";

        return [
            'facebook' => $facebook,
            'twitter' => $twitter,
            'whatsapp' => $whatsapp,
            'massenger' => $massenger,
        ];
    }
}
