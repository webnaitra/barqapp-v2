<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertiserResource;
use Illuminate\Http\Request;
use App\Http\Requests\Api\RegisterRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Category,
    NewsSource,
    Keyword,
    News,
    Advertiser,
    Favorite,
    UserSource,
    UserCategory,
    Adsense,
    AdsArea,
    Page,
    MobileAd
};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\ConfirmationCodeMail;
use App\Mail\SuggestionEmail;
use App\Mail\AddsScreenEmail;
use Mail;
use Illuminate\Support\Facades\Log;



class MobileApiController extends Controller
{

    public function all_category_list()
    {
        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "list" => []
        ];
    
        try {
            $featured_categories = [
                'اخبار',
                'إقتصاد',
                'رياضة',
                'تكنولوجيا',
                'سيارات',
                'سياحة',
                'ترفيه-وفن',  
                'ثقافة-ورآي',
                'أناقة-وجمال',
                'لايف-ستايل'
            ];
    
            $categories = Category::with('media')
                ->whereIn('slug', $featured_categories)
                ->orderBy('id', 'desc')    
                ->get();
    
            $uniqueCategories = $categories->unique('slug')->sortByDesc('created_at');
    
            if ($uniqueCategories->isEmpty()) {
                $data = ['status' => 'error', 'errormsg' => 'Not found'];
                return response()->json($data, 404);
            }
    
            foreach ($uniqueCategories as $category) {
                $cat = [
                    'id' => $category->id,
                    'link' => $this->get_category_url($category->slug),
                    'name' => $category->name,
                    'image' => $category->media->image_url ?? null, 
                    'color' => $category->color
                ];
                $data['list'][] = $cat;
            }
    
            return response()->json($data, 200);
    
        } catch (\Exception $e) {
            $data = [
                "status" => "error",
                "errormsg" => 'An error occurred while fetching categories',
                "error_code" => $e->getCode(),
                "list" => []
            ];
            return response()->json($data, 500);
        }
    }


    private function get_category_url(string $slug, ?string $parent_slug = null): string
    {
        if ($parent_slug) {
            return 'https://barqapp.net/category/' . $parent_slug . '/' . $slug;
        } else {
            return 'https://barqapp.net/category/' . $slug;
        }
    }
    

    public function child_category()
    {
        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "list" => []
        ];
    
        try {

            $featured_categories = array(
                'اخبار',
                'إقتصاد',
                'رياضة',
                'تكنولوجيا',
                'سيارات',
                'سياحة',
                'ترفيه-وفن',
                'ثقافة-ورآي',
                'أناقة-وجمال',
                'لايف-ستايل'
                // 'أخبار',
                // 'إقتصاد',
                // 'رياضة',
                // 'تكنولوجيا',
                // 'مركبات',
                // 'ترفيه',
                // 'فن',
                // 'ثقافة',
                // 'المرأة',
                // 'المنزل'
                // 'حكومي',  
                // 'إقتصاد',    
                // 'رياضة',
                // 'تكنولوجيا',
                // 'سيارات',
                // 'ترفية',
                // 'الصحة',
                // 'المنزل',
                // 'اسلاميات',
                // 'ثقافة',
                // 'سياحة',
                // 'التعليم',
                // 'ترفيه',
                // 'الرأي'
            );
            // Get categories that have children
            $categories = Category::whereIn('slug', $featured_categories)
            ->get();
    
            if ($categories->isEmpty()) {
                $data = ['status' => 'error', 'errormsg' => 'Not found'];
                return response()->json($data, 404);
            }
    
            foreach ($categories as $category) {
                $cat = [
                    'id' => $category->id,
                    'link' => $this->get_category_url($category->slug),
                    'name' => $category->name,
                    'color' => $category->color,

                ];
    
                $data['list'][] = $cat;
            }
    
            return response()->json($data, 200);
    
        } catch (\Exception $e) {
            $data = [
                "status" => "error",
                "errormsg" => 'An error occurred while fetching categories',
                "error_code" => $e->getCode(),
                "list" => []
            ];
            return response()->json($data, 500);
        }
    }
    

    public function all_source_list()
    {
        try {
            $distinctNames = NewsSource::distinct()->pluck('name');

            $newsSources = NewsSource::with('news', 'media')
                ->whereIn('name', $distinctNames)
                ->get();
                // dd($newsSources);
            if ($newsSources->isEmpty()) {
                $data = ['status' => 'error', 'msg' => 'Not found'];
                return response()->json($data, 404);
            }

            $sources = [];

            foreach ($newsSources as $item) {
                $sources[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'url' => $item->url,
                    'contact' => $item->contact,
                    'image' => $item->media->image_url ?? false, 
                    'icon' => $item->media->image_url ?? false,
                    'news_source_icon' => $item->media->image_url ?? false,
                    'facebook_url' => $item->facebook_url,
                    'twitter_url' => $item->twitter_url,
                ];
            }

            return $this->returnResponse(200, 'success', $sources, 'found');

        } catch (\Exception $e) {
            $data = [
                'status' => 'error',
                'msg' => 'An error occurred while fetching news sources',
                'error_code' => $e->getCode()
            ];
            return response()->json($data, 500);
        }
    }

    public function get_video_details()
    {

      try {
            $videos = News::select('id','name','date','video','image','source_id')->whereNotNull('video')
                    ->orderBy('id', 'desc')
                    ->take(20)
                    ->get();

            if ($videos->isEmpty()) {
                return response()->json([
                    'status' => 404,
                    'sub_message' => 'error',
                    'message' => 'No videos found',
                ], 404);
            }

            $formattedVideos = [];

            foreach ($videos as $video) {
                $formattedVideos[] = [
                    'id' => $video->id,
                    'name' => $video->name,
                    'date' => $video->date,
                    'video' => $video->video,
                    'image' => $video->image,
                    'news_source_icon' => $video->source->logo,
                    'news_source' => $video->source->name,
                    'image_url' => $video->image
                ];
            }
  
            $response = [
                'status' => 200,
                'sub_message' => 'success',
                'return' => $formattedVideos,
                'message' => 'found'
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'sub_message' => 'error',
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function all_country_list()
    {

        $data = ['status' => 'ok', 'errormsg' => '', 'error_code' => ''];

        $countries = News::with(['category', 'subcategory'])
        ->select('news_country')
        ->orderBy('id', 'DESC')
        ->distinct()
        ->paginate(20);

        if ($countries->isEmpty()) {
            $data = ['status' => 'error', 'msg' => 'Not found'];
            return response()->json($data, 404);
        }

        $countryArray = [];
        foreach ($countries as $country) {
            if ($country->news_country !== null) {
                $countryArray[] = ['name' => $country->news_country];
            }
        }

        $data['country'] = $countryArray;

        return response()->json($data, 200);

    }

    public function category_menu()
    {
        try {
            $data = [
                "status" => "ok",
                "errormsg" => "",
                "error_code" => "",
                "category_list" => []
            ];

            $featured_categories = array(
                'اخبار',
                'إقتصاد',
                'رياضة',
                'تكنولوجيا',
                'سيارات',
                'سياحة',
                'ترفيه-وفن',
                'ثقافة-ورآي',
                'أناقة-وجمال',
                'لايف-ستايل'
                // 'أخبار',
                // 'إقتصاد',
                // 'رياضة',
                // 'تكنولوجيا',
                // 'مركبات',
                // 'ترفيه',
                // 'فن',
                // 'ثقافة',
                // 'المرأة',
                // 'المنزل'
                // 'حكومي',  
                // 'إقتصاد',    
                // 'رياضة',
                // 'تكنولوجيا',
                // 'سيارات',
                // 'ترفية',
                // 'الصحة',
                // 'المنزل',
                // 'اسلاميات',
                // 'ثقافة',
                // 'سياحة',
                // 'التعليم',
                // 'ترفيه',
                // 'الرأي'
            );

            $categories = Category::with('media')
            ->whereIn('slug', $featured_categories)
            ->get();

            $uniqueCategories = $categories->unique('slug');


            if ($uniqueCategories->isEmpty()) {
                $data = ['status' => 'error', 'msg' => 'Not found'];
                return response()->json($data, 404);
            }

            foreach ($uniqueCategories as $category) {
                $cat = [
                    'id' => $category->id,
                    'link' => $this->get_category_url($category->slug),
                    'name' => $category->name,
                    'color' => $category->color,
                    'image' => $category->media->image_url ?? false,
                ];

                $data['category_list'][] = $cat;
            }

            return response()->json($data, 200);

        } catch (\Exception $e) {
            $data = [
                'status' => 'error',
                'errormsg' => 'An error occurred while fetching categories',
                'error_code' => $e->getCode()
            ];
            return response()->json($data, 500);
        }
    }

    public function mobileApiMakeNewAuthor(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:advertisers,adv_email',
            // 'username' => 'required|alpha_dash|unique:advertisers,adv_username',
            'password' => 'required|string|min:8',
        ]);

        // dd($request->all());
        
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);    
        }

        $password = Hash::make($request->password);

        $advertiser = Advertiser::updateOrCreate([
            'adv_first_name' => $request->first_name,
            'adv_last_name' => $request->last_name,
            'adv_username' => $request->username,
            'adv_email' => $request->email,
            'adv_password' => $password,
        ]);
        if (!empty($_FILES['image'])) {
            $imageId = upload($request, 'image', 'image');
            if (!empty($imageId)) {
                $advertiser->image = $imageId;
            }
        }
        $advertiser->save();
        $token = $advertiser->createToken('Laravel Password Grant Client Mobile')->accessToken;
        return $this->returnResponse(200, 'success', ['data' =>
        [
            'status' => 'ok',
            'token' => $token
        ]], 'Registered');
    }

    public function mobileloginapi(Request $request)
    {
        $emailOrUsername = $request->input('username');

        $password = $request->password;
       
        try {
            $advertiser = Advertiser::where(function ($query) use ($emailOrUsername) {
                $query->where('adv_email', $emailOrUsername)
                      ->orWhere('adv_username', $emailOrUsername);
            })->first();

           if (!$advertiser) {
                return response()->json(['status' => 400, 'message' => 'Invalid credentials']);
            }

            if (!Hash::check($password, $advertiser->adv_password)) {
                return response()->json(['status' => 400, 'message' => 'Wrong Password']);
            }
            $token = $advertiser->createToken('Laravel Password Grant Client')->accessToken;
            return $this->returnResponse(200, 'success', ['data' =>
            [
                'user' => new AdvertiserResource(($advertiser)),
                'token' => $token
            ]], 'Logined');

        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => $e->getMessage()]);
        }
    }

    public function forgotPasswordMob(Request $request)
    {
        $request = $request->all();
        $email = (!empty($request['email'])) ? $request['email'] : '';
        if (empty($email)) {
            return response()->json(['status' => 'error', 'message' => 'Email is required']);
        }
        $advertiser = Advertiser::where('adv_email', $email)->first();
        if (empty($advertiser)) {
            return response()->json(['status' => 'error', 'message' => 'User does not exist']);

        }
        $advertiser->adv_forgot_password_code = generateRandomInteger(30);
        $advertiser->save();

        try {
            Mail::to($email)->send(new ConfirmationCodeMail($advertiser->adv_forgot_password_code));
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
        
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send confirmation email. Reason: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'message' => 'A reset password code has been sent to your email',
        ]);
    }

    public function resetPasswordMob(Request $request)
    {
        $request = $request->all();
        // dd($request);
        $code = (!empty($request['code'])) ? $request['code'] : '';
        $password = (!empty($request['password'])) ? $request['password'] : '';
        if (empty($code)) {
            return response()->json(['status' => 'error', 'message' => 'Code is required']);
        }
        $advertiser = Advertiser::where('adv_forgot_password_code', $code)->first();
        if (empty($advertiser)) {
            return response()->json(['status' => 'error', 'message' => 'User does not exist']);
        }
        if (strlen($password) < 8) {
            return response()->json(['status' => 'error', 'message' => 'Email is already used']);
        }
        $advertiser->adv_forgot_password_code = NULL;
        $advertiser->password = Hash::make($password);
        $advertiser->save();

        return response()->json(['status' => 'success', 'message' => 'The password has been Changed successfully']);

    }


    public function get_home_post(Request $request)
    {
        $search = $request->search_keyword;

        // if (!$search) {
        //     $data = ['status' => 'error', 'msg' => ' search_keyword is required'];
        //     return response()->json($data, 400);
        // }  

        // dd($search);
        
        $newsItems = News::with(['category', 'subcategory'])
        ->select("id", 'name', 'content', 'image', 'category_id',  'views', 'shares', 'urgent', 'video', 'source_id', 'source_link','source_icon', 'created_at')
        ->where('name', 'like', '%' . $search . '%')
        ->orderBy('created_at', 'desc') 
        ->paginate(100);

        // dd($newsItems);


        if ($newsItems->isEmpty()) {
            $data = ['status' => 'error', 'msg' => 'Not found'];
            return response()->json($data, 404);
        }

        $formattedData = [];
        foreach ($newsItems as $item) {
            $formattedData[] = [
                "id" => $item->id,
                "title" => $item->name,
                "content" => $item->content,
                "url" => $item->source_link,
                "image" => $item->image,
                "date" => date('d/m/Y', strtotime($item->created_at)),
                "source_url" => $item->source_url,
                "source_icon" => $item->source->logo, 
                "color" => $item->category->color, 
                "cat_id" => $item->category_id, 
                "category" => $item->category->name, 
            ];
        }

        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "post" => $formattedData
        ];

        return response()->json($data);
   
    }

    private function returnResponse($code, $sub, $return, $message)
    {
        $response = array("status" => $code, "sub_message" => $sub, "return" => $return, "message" => $message);
        return response($response);
    } 

    public function single_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:news,id', 
        ]);
    
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);    
        }

        $mainPost = News::with(['category', 'tags'])->findOrFail($request->post_id);
        // dd($mainPost);
    
        $relatedNews = News::with(['category', 'tags'])
            ->where('category_id', $mainPost->category_id)
            ->where('id', '!=', $mainPost->id) 
            ->inRandomOrder()
            ->take(10)
            ->get();
    
        $data = [
            'image' => $mainPost->image ? url($mainPost->image) : false,
            'source_icon' => $mainPost->source->logo,
            'category' => $mainPost->category->name,
            'title' => $mainPost->name,
            'id' => $mainPost->id,
            'content' => $mainPost->content,
            'video_url' => $mainPost->video,
            'source' => $mainPost->source->name,
            'tag' => $mainPost->tags->map(function($tag) {
                return [
                    "slug" => "",
                    'name' => $tag->tag_name
                ];
            })->toArray(),
            'site_url' => $mainPost->source_link
        ];
    
        $relatedPosts = $relatedNews->map(function($news) {
            return [
                'rel_id' => $news->id,
                'rel_title' => $news->name,
                'rel_link' => $news->news_link ? url($news->news_link) :false,
                'rel_url' => $news->url ? url($news->url) : false,
                'rel_date' => $news->date,
                'rel_source_url' => $news->source_link
            ];
        })->toArray();
    
        $response = [
            'data' => $data,
            'related_post' => $relatedPosts
        ];
    
        return response()->json($response, 200);
    }
    
    

    public function get_category_post(Request $request)
    {
        try {
                $categoryName = $request->input('category');
                //$categoryName = 'ترفيه-وفن';

                if (!$categoryName) {
                    $data = ['status' => 'error', 'msg' => 'Category name is required'];
                    return response()->json($data, 400);
                }
                
                $category = Category::where('slug', $categoryName)
                ->orWhere('name', $categoryName)
                ->first();

                // dd($category);               
                if (!$category) {
                    $data = ['status' => 'error', 'msg' => 'Category not found'];
                    return response()->json($data, 404);
                }
                
                $newsItems = News::with(['category', 'subcategory'])
                ->select('id', 'name', 'content', 'image', 'category_id',  'views', 'shares', 'urgent', 'video', 'source_id', 'source_link', 'created_at')
                ->where('category_id', $category->id)->orWhere( $category->id)
                ->orderBy('created_at', 'desc')
                ->paginate(50);

                // dd($newsItems);

                $formattedData = [];
                
                foreach ($newsItems as $item) {
                    $formattedItem = [
                        "id" => $item->id,
                        "title" => $item->name,
                        "content" => $item->content,
                        "url" => $item->source_link,
                        "image" => $item->image,
                        "date" => date('d/m/Y', strtotime($item->created_at)),
                        "source_url" => $item->source->name,
                        "source_icon" => $item->source->logo,
                        "color" => $item->category->color, 
                        "category" => $item->category->name,
                        "child" => [] 
                    ];

                    $subcategories = is_a($item->subcategory, 'Illuminate\Database\Eloquent\Collection') ? $item->subcategory : [$item->subcategory];

                    foreach ($subcategories as $subcategory) {
                        if ($subcategory) { 
                            $formattedItem['child'][] = [
                                "term_id" => $subcategory->id,
                                "name" => $subcategory->name,
                                "slug" => $subcategory->slug,
                                "term_group" => $subcategory->term_group,
                                "term_taxonomy_id" => $subcategory->cat_parent_id,
                                "taxonomy" => "category",
                                "description" => "",
                                "parent" => 2,
                                "count" => 306,
                                "filter" => "raw"
                            ];
                        }
                    }

                    $formattedData[] = $formattedItem;
                }

                // dd($formattedData);
                
                $data = [
                    "status" => "ok",
                    "errormsg" => "",
                    "error_code" => "",
                    "posts" => $formattedData
                ];
                
                return response()->json($data);

            } catch (\Exception $e) {
                \Log::error('Error fetching category posts: ' . $e->getMessage());
        
                $data = [
                    "status" => "error",
                    "msg" => "An error occurred while fetching category posts.",
                    "error_code" => $e->getCode()
                ];
        
                return response()->json($data, 500);
            }

    }

    public function country_wise(Request $request)
    {
        try {
            $param = $request->all();

            if (empty($param['country_name'])) {
                return response()->json([
                    'status' => 'error',
                    'errormsg' => 'country_name is required',
                ], 400);
            }

            $countryName = $param['country_name'];

            $data = [
                'status' => 'ok',
                'errormsg' => '',
                'error_code' => ''
            ];

            $subQuery = News::select('news_source', \DB::raw('MAX(id) as id'))
                ->where('news_country', $countryName)
                ->groupBy('news_source');

            $newsItems = News::whereIn('id', $subQuery->pluck('id'))
                ->orderBy('created_at', 'desc')
                ->paginate(15);

                // dd($newsItems);

            if ($newsItems->isNotEmpty()) {
                $data['country'] = $newsItems;
                return response()->json($data, 200);
            } else {
                $data['status'] = 'error';
                $data['errormsg'] = 'No data found for the specified country.';
                return response()->json($data, 404);
            }
        } catch (Exception $e) {
            $data = [
                'status' => 'error',
                'errormsg' => $e->getMessage(),
                'error_code' => $e->getCode()
            ];
            return response()->json($data, 500);
        }
    }


    private function missingParameter()
    {
        $response = array("status" => 404, "sub_message" => "failure", "return" => array(), "message" => "Missing Parameters");
        return response($response);
    }

    public function add_bookmark(Request $request)
    {
        $favUserId = request()->userid;
        $newsId = request()->postid;
        if (empty($favUserId) || empty($newsId)) {
            return $this->missingParameter();
        }
        $user = Advertiser::find($favUserId);
        if (empty($user)) {
            return response()->json(['status' => 'error', 'message' => 'User does not exist']);
            
        }
        $newsItem = News::find($newsId);
        if (empty($newsItem)) {
            return response()->json(['status' => 'error', 'message' => 'News does not exist']);
        }

        $oldFavorite = Favorite::where('fav_news_id', $newsId)
            ->where('fav_user_id', $favUserId)->first();
        if (!empty($oldFavorite)) {
            return response()->json(['status' => 'error', 'message' => 'The user has this news in his favorites']);
        }
        $favorite = new Favorite;
        $favorite->fav_user_id = $favUserId;
        $favorite->fav_news_id = $newsId;
        $favorite->save();

        return response()->json(['status' => 'success', 'message' => 'Inserted successfully']);
        
    }

    public function getbookmarks(Request $request)
    {
        try {
            
            if (!$request->has('user_id') || $request->user_id === null) {
                return response()->json(['error' => 'User ID is required'], 400);
            }
    
            $favUserId = $request->user_id;
    
            $favNewsIds = Favorite::where('fav_user_id', $favUserId)
                ->pluck('fav_news_id')
                ->toArray();

                // dd($favNewsIds);
    
            if (empty($favNewsIds)) {
                return response()->json(['message' => 'No bookmarks found for the user'], 404);
            }
    
            $news = News::whereIn('id', $favNewsIds)
                ->select(
                    "id", 'name', 'content', 'image', 'category_id',
                     'views', 'shares', 'urgent', 
                    'video', 'source_id', 'source_link', 'created_at'
                )
                ->take(20)
                ->get();
    
            if (empty($news)) {
                return response()->json(['message' => 'No news items found'], 404);
            }
    
            return response()->json($news, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function check_source(Request $request)
    {
        $data = [
            'status' => 'ok',
            'errormsg' => '',
            'error_code' => ''
        ];
        
        $userId = $request->user_id;
        $sourceName = $request->source_name;

        $source = NewsSource::where('name', $sourceName)->first();

        if (empty($userId) || empty($source)) {
            return $this->missingParameter();
        }

        $user = Advertiser::find($userId);
        if (empty($user)) {
            return response()->json(['status' => 'error', 'message' => 'User does not exist']);
        }

        if (empty($source)) {
            return response()->json(['status' => 'error', 'message' => 'Source does not exist']);
        }
        
        $oldSource = UserSource::where('source_id', $source->id)
            ->where('user_id', $userId)->first();
        if (!empty($oldSource)) {
            return response()->json(['status' => 'error', 'message' => 'The user has this source in his favorites']);
        }

        $userSource = new UserSource;
        $userSource->user_id = $userId;
        $userSource->source_id = $source->id;
        $userSource->save();

        return response()->json(['status' => 'success', 'message' => 'Inserted successfully']);
    }

    
    public function get_user_source(Request $request)
    {

        $userId = $request->input('user_id');
    
        if (!$userId) {
            $data = ['status' => 'error', 'msg' => 'User ID is required'];
            return response()->json($data, 400);
        }
    
        $newsSource = UserSource::where('user_id', $userId)->pluck('source_id');
        
        if (empty($newsSource)) {
            $data = ['status' => 'error', 'msg' => 'No news sources found for the user'];
            return response()->json($data, 404);
        }
    
        $newsItems = NewsSource::whereIn('id', $newsSource)->take(30)->get(); 
    
    
        if ($newsItems->isEmpty()) {
            $data = ['status' => 'error', 'msg' => 'No news items found for the user'];
            return response()->json($data, 404);
        }
    
        $formattedData = [];
        foreach ($newsItems as $item) {
            $formattedData[] = [
                "source_name" => $item->name,
            ];
        }
    
        $responseData = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "list" => $formattedData
        ];
    
        return response()->json($responseData);
        
    }

    public function adds_screen(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => ""
        ];
    
        $name = $request->input('name');
        $email = $request->input('email');
        $content = $request->input('message');
    
        $to = "barqappnews@gmail.com";
    
        try {
            Mail::to($to)->send(new AddsScreenEmail($name, $email, $content));
            return response()->json($data, 200);
        } catch (\Exception $e) {
            $data = [
                "status" => "error",
                "msg" => "Failed to send email: " . $e->getMessage()
            ];
            return response()->json($data, 500);
        }
    }

    public function add_suggest(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $name = $request->input('name');
        $email = $request->input('email');
        
        $to = "barqappnews@gmail.com";
    
        try {
            Mail::to($to)->send(new SuggestionEmail($name, $email));
            
            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'msg' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }

    public function get_source_post(Request $request)
    {
        $newsName = $request->input('slug');

        $newsItems = News::with(['category', 'subcategory'])
        ->select("id", 'name', 'content', 'image', 'category_id',  'views', 'shares', 'urgent', 'video', 'news_source', 'source_link','news_source_icon','created_at')
        ->where('name', 'like', '%' . $newsName . '%') // Filter by news name
        ->distinct('news_source')
        ->paginate(100);

        // dd($newsItems);

        if ($newsItems->isEmpty()) {
            $data = ['status' => 'error', 'msg' => 'Not found'];
            return response()->json($data, 404);
        }
            

        $formattedData = [];
        foreach ($newsItems as $item) {
            $formattedData[] = [
                "id" => $item->id,
                "title" => $item->name,
                "content" => $item->content,
                "url" => $item->source_link,
                "image" => $item->image,
                "date" => date('d/m/Y', strtotime($item->created_at)),
                "excerpt"=> $item->excerpt,
                "source_name" => $item->source->name,
                "source_url" => $item->source_link,
                "source_icon" => $item->source->logo, 
                "color" => $item->category->color, 
                "tag" => false, 
                "category" => $item->category->name, 
            ];
        }

        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "posts" => $formattedData
        ];

        return response()->json($data);

    }

    public function search_news(Request $request)
    {
        $newsName = $request->input('slug');

        if (!$newsName) {
            $data = ['status' => 'error', 'msg' => 'news name is required'];
            return response()->json($data, 400);
        }        

        $newsItems = News::with(['category', 'subcategory'])
        ->select("id", 'name', 'content', 'image', 'category_id',
             'views', 'shares', 'urgent', 'video', 'news_source', 'source_link','news_source_icon', 'created_at')
            ->where('name', 'like', '%' . $newsName . '%') 
            ->take(20)
            ->get();
            
        if ($newsItems->isEmpty()) {
            $data = ['status' => 'error', 'msg' => 'Not found'];
            return response()->json($data, 404);
        }
            

        $formattedData = [];
        foreach ($newsItems as $item) {
            $formattedData[] = [
            "id" => $item->id,
            "title" => $item->name,
            "content" => $item->content,
            "url" => $item->source_link,
            "image" => $item->image,
            "date" => date('d/m/Y', strtotime($item->created_at)),
            "excerpt"=> $item->excerpt,
            "source_name" => $item->source->name,
            "source_url" => $item->source_link,
            "source_icon" => $item->source->logo, 
            "color" => $item->category->color, 
            "category" => $item->category->name, 
            ];
        }
        
        $data = [
            'status' => 'ok',
            'errormsg' => '',
            'error_code' => '',
            'posts' => $formattedData
        ];

        return response()->json($data);

    }

    public function retrivePassword(Request $request)
    {
        // dd($request->all());
        $request = $request->all();
        $email = (!empty($request['email'])) ? $request['email'] : '';
        if (empty($email)) {
            return response()->json(['status' => 'error', 'message' => 'Email is required']);
        }
        $advertiser = Advertiser::where('adv_email', $email)->first();
        if (empty($advertiser)) {
            return response()->json(['status' => 'error', 'message' => 'User does not exist']);

        }
        $advertiser->adv_forgot_password_code = generateRandomInteger(30);
        $advertiser->save();

        try {
            Mail::to($email)->send(new ConfirmationCodeMail($advertiser->adv_forgot_password_code));
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
        
            return response()->json([
                'status' => 500,
                'message' => 'Failed to send confirmation email. Please try again later.'
            ], 500);
        }

        
        return response()->json([
            'status' => 200,
            'message' => 'A reset password code has been sent to your email',
        ]);
    }

    public function get_country_post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $data = ['status' => 'ok', 'errormsg' => '', 'error_code' => ''];

        $country = $request->country;
        
        $newsItems = News::with(['category', 'subcategory'])
            ->where('news_country', $country)
            ->orderBy('id', 'DESC')
            ->paginate(20);

        if ($newsItems->isEmpty()) {
            $data = ['status' => 'error', 'msg' => 'Not found'];
            return response()->json($data, 404);
        }

        $newsArray = [];
        foreach ($newsItems as $newsItem) {
            $newsData['id'] = $newsItem->id;
            $newsData['title'] = $newsItem->name;
            $newsData['content'] = $newsItem->content;
            $newsData['url'] = $newsItem->source_link;
            $newsData['image'] = $newsItem->image; 
            $newsData['date'] = $newsItem->created_at->format('Y-m-d');
            $newsData['source_url'] = $newsItem->source_link;
            $newsData['source_icon'] = $newsItem->source->logo;
            $newsData['category'] = $newsItem->category->name;
            $newsArray[] = $newsData;
        }

        $data['posts'] = $newsArray;
        return response()->json($data, 200);

    }

    public function fav_menu_cate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'cat_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $data = [
             "status" => "ok",
             "errormsg" => "",
             "error_code" => ""
         ];

       $userId = $request->user_id;
       $catId = $request->cat_id;

       $user = Advertiser::find($userId);
       if (empty($user)) {
           return response()->json(['status' => 'error', 'message' => 'User does not exist']);
           
       }

       $cat = Category::find($catId);
       if (empty($cat)) {
           return response()->json(['status' => 'error', 'message' => 'Category does not exist']);
       }
       

       $oldFavorite = UserCategory::where('cat_id', $catId)
       ->where('user_id', $userId)->first();
        if (!empty($oldFavorite)) {
            return response()->json(['status' => 'error', 'message' => 'The user has this category in his favorites']);
        }

       $favCat = new UserCategory;
       $favCat->user_id = $userId;
       $favCat->cat_id = $catId;
       $favCat->save(); 

        return response()->json($data);
    }

    public function get_fav_menu_cate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg' => 'Validation failed',
                'errors' => $validator->errors()->all()
            ], 422);
        }

        $userId = $request->user_id;

        $user = Advertiser::find($userId);
        if (empty($user)) {
            return response()->json(['status' => 'error', 'message' => 'User does not exist']);
            
        }

        $favCat = UserCategory::where('user_id',$userId)->pluck('cat_id');

        // dd( $favCat);

         $cat = Category::whereIn('id', $favCat)->get();

        $formattedData = [];
        foreach ($cat as $item) {
            $formattedData[] = [
                "name" => $item->name,
            ];
        }

        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "user_selected" => $formattedData
        ];

        return response()->json($data);
    }

    public function sports()
    {
        $data = [
            'status' => 'ok',
            'errormsg' => '',
            'error_code' => '',
            'sports' => []
        ];

        $posts = News::whereHas('category', function ($query) {
            $query->where('name', 'رياضة');
        })->orderBy('id', 'desc')->paginate(15);

        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result Not Found',
            ], 404); 
        }
        // dd($posts);

        foreach ($posts as $post) {
            $postdata = [
                'id' => $post->id,
                'title' => $post->name,
                'url' => $post->source_link,
                'image' => $post->image, 
                'color' => $post->category->color, 
                'category' => $post->category->name
            ];

            $data['sports'][] = $postdata;
        }

        return response()->json($data, 200);
        
    }

    public function economy(Request $request)
    {
        $data = [
            'status' => 'ok',
            'errormsg' => '',
            'error_code' => '',
            'economy' => []
        ];


        $posts = News::whereHas('category', function ($query) {
            $query->where('name', 'إقتصاد');
        })->orderBy('id', 'desc')->paginate(15);

        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result Not Found',
            ], 404); 
        }

        // dd($posts);

        foreach ($posts as $post) {
            $postdata = [
                'id' => $post->id,
                'title' => $post->name,
                'url' => $post->source_link,
                'image' => $post->image, 
                'color' => $post->category->color, 
                'category' => $post->category->name
            ];

            $data['economy'][] = $postdata;
        }

        return response()->json($data, 200);
    }

    public function health(Request $request)
    {
        $data = [
            'status' => 'ok',
            'errormsg' => '',
            'error_code' => '',
            'health' => []
        ];

        $posts = News::whereHas('subcategory', function ($query) {
            $query->whereIn('name', ['معلومات طبية']);
        })->orderBy('id', 'desc')->paginate(15);

        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result Not Found',
            ], 404); 
        }

        foreach ($posts as $post) {
            $postdata = [
                'id' => $post->id,
                'title' => $post->name,
                'url' => $post->source_link,
                'image' => $post->image, 
                'color' => $post->category->color, 
                'category' => $post->category->name
            ];

            $data['health'][] = $postdata;
        }

        return response()->json($data, 200);
    }

    public function tourism(Request $request)
    {
        $data = [
            'status' => 'ok',
            'errormsg' => '',
            'error_code' => '',
            'tourism' => []
        ];

        $posts = News::whereHas('category', function ($query) {
            $query->where('name', 'سياحة');
        })->orderBy('id', 'desc')->paginate(15);

        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result Not Found',
            ], 404); 
        }
        
        // dd($posts);  

        foreach ($posts as $post) {
            $postdata = [
                'id' => $post->id,
                'title' => $post->name,
                'url' => $post->source_link,
                'image' => $post->image, 
                'color' => $post->category->color, 
                'category' => $post->category->name
            ];

            $data['tourism'][] = $postdata;
        }

        return response()->json($data, 200);
    }


    public function delete_user(Request $request)
    {
        // dd($request->all());
        $user = Advertiser::where('adv_email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json([
        'status' => 'success',
        'message' => 'User deleted successfully'], 200);
    }

    public function home_simple()
    {
        $response = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "posts" => []
        ];


        $newsItems = News::with(['category', 'subcategory'])
        ->select("id", 'name', 'content', 'image', 'category_id',
             'views', 'shares', 'urgent', 'video', 'news_source', 'source_link','news_source_icon','created_at')
        ->orderBy('created_at', 'desc')
        ->paginate(100);

        // dd($newsItems);
        if ($newsItems->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result Not Found',
            ], 404); 
        }
        
        foreach ($newsItems as $item) {
            $post = [
                "id" => $item->id,
                "title" => $item->name,
                "content" => $item->content,
                "url" => $item->url,
                "image" => $item->image,
                "date" => $item->created_at->format('d/m/Y'),
                "source_url" => $item->source->name,
                "source_icon" => $item->source->logo,
                "color" => $item->category->color,
                "child" => []
            ];
        
            $post["category"] = $item->category->name;
        
            $response["posts"][] = $post;
        }
        
        return response()->json($response);
    }

    public function breaking_news(Request $request)
    {
        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => "",
            "category1" => [],
            "category2" => [],
            "category3" => []
        ];

        $postsCategory1 = News::whereHas('category', function ($query) {
            $query->where('name', 'أخبار');
        })->orderBy('id', 'DESC')->take(4)->get();

        foreach ($postsCategory1 as $post) {
            $data['category1'][] = $this->formatPostData($post);
        }

        $postsCategory2 = News::whereHas('category', function ($query) {
            $query->where('name', 'رياضة');
        })->orderBy('id', 'DESC')->take(4)->get();

        foreach ($postsCategory2 as $post) {
            $data['category2'][] = $this->formatPostData($post);
        }

        $postsCategory3 = News::whereHas('category', function ($query) {
            $query->where('name', 'المرأة');
        })->orderBy('id', 'DESC')->take(4)->get();

        foreach ($postsCategory3 as $post) {
            $data['category3'][] = $this->formatPostData($post);
        }

        return response()->json($data);
    }

    private function formatPostData($post)
    {
        return [
            "id" => $post->id,
            "title" => $post->name,
            "url" => $post->source_link,
            "date" => $post->created_at->format('d/m/Y'),
            "color" => $post->category->color,
            "category" => $post->category->name
        ];
    }

    public function get_user_selected()
    {
        $data = ['status' => 'ok', 'errormsg' => '', 'error_code' => ''];

        //requested news source
        $userId = 1;

        $user = News::where('news_user_id',$userId)->first();

        // dd($user);

        if (!$user) {
            return response()->json('User Not Found', 404);
        }
    
        $source = News::where('news_source', $user['news_source'])
        ->take(25)
        ->get()
        ->toArray();

        $sourceArray = [];
        foreach ($source as $sourceData) {
            $sourceArray[] = ['slug' => $sourceData['slug']];
        }
    
        $relatedArray = [];
    
        foreach ($source as $post) {
            $relData = [
                'rel_id' => $post['id'],
                'rel_title' => $post['name'], 
                'rel_url' => $post['image'], 
                'rel_link' => $post['source_link'], 
                'rel_content' => $post['content'], 
                'rel_date' => $post['created_at'],
                'rel_cat' => $post['category_id'] 
            ];
            $relatedArray[] = $relData;
        }
    
        $data['source'] = $sourceArray;
        $data['news'] = $relatedArray;
    
        return response()->json($data, 200);
    }

    public function header_add(Request $request)
    {
        if ($request->filled('image') || $request->filled('name') || $request->filled('url')) {

            $newMobileAd = new MobileAd();
            $newMobileAd->image = $request->image; 
            $newMobileAd->name = $request->name; 
            $newMobileAd->url = $request->url; 
            $newMobileAd->save();
    
            $mobileAds = MobileAd::orderBy('created_at', 'desc')->take(4)->get();
        } else {
            $mobileAds = MobileAd::orderBy('created_at', 'desc')->take(4)->get();
        }
    
        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => ""
        ];
    
        for ($i = 0; $i < count($mobileAds); $i++) {
            $data[$i + 1 . "th_ads_img"] = $mobileAds[$i]->image;
            $data[$i + 1 . "th_ads_name"] = $mobileAds[$i]->name;
            $data[$i + 1 . "th_ads_link"] = $mobileAds[$i]->url;
        }
    
        if (isset($newMobileAd)) {
            $data['new_ad_img'] = $newMobileAd->image ;
            $data['new_ad_name'] = $newMobileAd->name;
            $data['new_ad_link'] = $newMobileAd->url;
        }
    
        return response()->json($data, 200);

    }

    public function search_by_cat_news(Request $request)
    {
        $user_id = $request->input('user_id');
        $slug = $request->input('search_keyword');
        $source = $request->input('source');
        $category = $request->input('category');
    
        $query = News::query();
    
        $query->where(function ($query) use ($user_id, $slug, $source, $category) {
            if ($user_id !== null) {
                $query->where('news_user_id', $user_id);
            }
            if ($slug !== null) {
                $query->where(function ($query) use ($slug) {
                    foreach (explode(' ', $slug) as $keyword) {
                        $query->orWhere('slug', 'LIKE', "%$keyword%");
                    }
                });
            }
            if ($source !== null) {
                $query->where('news_source', $source);
            }
            if ($category !== null) {
                $query->whereHas('category', function ($query) use ($category) {
                    $query->where('name', $category);
                });
            }
        });
    
        $newsItems = $query->orderBy('id', 'desc')->take(20)->get();
        // dd($newsItems);
    
        $responseArray = [];
        foreach ($newsItems as $newsItem) {
            $postdata = [
                'id' => $newsItem->id,
                'title' => $newsItem->name, 
                'content' => $newsItem->content, 
                'url' => $newsItem->source_link, 
                'image' => $newsItem->image, 
                'date' => $newsItem->date, 
                'excerpt' => $newsItem->excerpt, 
                'source_url' => $newsItem->source_link, 
                'source_icon' => $newsItem->source->logo, 
                'color' => $newsItem->category->color, 
                'category' => $newsItem->category->name, 
            ];
            $responseArray[] = $postdata;
        }
    
        return response()->json($responseArray);
    }

    public function barqapp_list(Request $request)
    {
        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => ""
        ];

        $posts = News::orderBy('id', 'desc')->take(20)->get();
        // dd($posts);

        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Result Not Found',
            ], 404); 
        }

        $productArray = [];
        foreach ($posts as $post) {
            $postdata = [
                'id' => $post->id,
                'title' => $post->name,
                'image' => $post->image, 
            ];
            $productArray[] = $postdata;
        }
    
        $data['posts'] = $productArray;

        return response()->json($data, 200);
    }

    public function about()
    {
        $data = [
            "status" => "ok",
            "errormsg" => "",
            "error_code" => ""
        ];

        $about = Page::where('page_slug', 'about-US')->first();

        if (!$about) {
            return response()->json('Page Not Found', 404);
        }

        $data['about'] = $about;

        return response()->json($data);

    }

    public function getLoggedUser()
    {
        // dd('done');
        if(Auth::guard('api')->check()){
            $user = Auth::guard('api')->user();
            return response(['data' => $user],200);
        }
        return Response(['data' => 'unauthenticated'],200);
    }
  
}
