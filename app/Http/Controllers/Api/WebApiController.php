<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\AdvertiserResource;
use Illuminate\Http\Request;
use App\Http\Requests\Api\RegisterRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Alkoumi\LaravelHijriDate\Hijri;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use ArPHP\I18N\Arabic;
use App\Settings\GeneralSettings;

use App\Models\{
    Interest,
    News,
    Source,
    SourceFeed,
    Category,
    Adsense,
    Advertiser,
    Page,
    Tag,
    Contact,
    Keyword,
    Whatsapp,
    Newsletter,
    Favorite,
    Menu,
    AdsArea,
    LiveStream,
    ProductCategory,
    Country,
    Affiliate,
    AdminAd,
    Video
};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use App\Mail\ConfirmationCodeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Mail;

class WebApiController extends Controller
{
    public $table_prefix;
    public $meta_fields = array();

    public function __construct(Request $request)
    {
        // DB::select("ALTER TABLE advertisers ADD adv_player_ids TEXT NULL");
        $this->app_lang = (!empty($request->app_lang)) ? $request->app_lang : "ar";
        App::setLocale($this->app_lang);
    }



    public function getServer(GeneralSettings $settings)
    {

        $page = request()->input('page', '');
        $categories = Category::select("id", 'name', 'order', 'slug', 'color')
            ->orderBy("order", 'asc');
        $categories = $categories->get();
        $categories = $categories->toArray();
        $countries = Country::get();


        $userId = request()->header('X-User-ID');
        $country = request()->input('country', 'EG');
        // dd($requestedCountry);
        $user = null;
        if ($userId) {
            $advertiser = Advertiser::find($userId);
            if (!empty($advertiser)) {
                $this->table_prefix = "adv_";
                $user = new AdvertiserResource($advertiser);
            }
        }

        $user_sources = [];

        if ($user) {
            $sources = $user->sources()->select('sources.id', 'sources.arabic_name')->get();

            foreach ($sources as $source) {
                $user_sources[] = [
                    'id' => $source->id,
                    'name' => $source->arabic_name
                ];
            }
        }


        // Get footer menus
        $menus = Menu::select("id", 'name')
            ->orderBy("id", 'asc')->get();

        foreach ($menus as $menu) {
            $menu->menu_items = $menu->menuItems()->select('id', 'name', 'type', 'url', 'target')->orderBy('order', 'asc')->get();
        }

        $ads = Adsense::with(['location' => function ($query) {
            $query->select('name');
        }])->select("id", 'name', 'code')->get();

        
        $facebook = $settings->app_facebook ?? "";
        $twitter = $settings->app_twitter ?? "";
        $whatsapp = $settings->app_whatsapp ?? "";
        $massenger = $settings->app_massenger ?? "";
        $instagram = $settings->app_instagram ?? "";
        $youtube = $settings->app_youtube ?? "";
        $tiktok = $settings->app_tiktok ?? "";

        $social_links = [
            'facebook' => $facebook,
            'twitter' => $twitter,
            'whatsapp' => $whatsapp,
            'massenger' => $massenger,
            'instagram' => $instagram,
            'youtube' => $youtube,
            'tiktok' => $tiktok,
        ];
        Carbon::setLocale('ar');
        $date = Carbon::now();
        $hijri_date = Hijri::MediumDate();


        // get static arrays


        $static_array = array(
            'footer_text',
            'newsletter_text',
            'app_google_play',
            'app_app_store',
            'copyright',
            'app_logo',
            'app_header',
            'app_download_text',
        );


        if($page == 'contact'){

            $contact_title = $settings->contact_title ?? "";
            $contact_introduction = $settings->contact_introduction ?? "";
            $contact_form_title = $settings->contact_form_title ?? "";
            $contact_form_introduction = $settings->contact_form_introduction ?? "";
            $contact_address_title = $settings->contact_address_title ?? "";
            $contact_address_content = $settings->contact_address_content ?? "";
            $contact_email_title = $settings->contact_email_title ?? "";
            $contact_email_content = $settings->contact_email_content ?? "";
            $contact_phone_title = $settings->contact_phone_title ?? "";
            $contact_phone_content = $settings->contact_phone_content ?? "";
            $static_array[] = 'contact_title';
            $static_array[] = 'contact_introduction';
            $static_array[] = 'contact_form_title';
            $static_array[] = 'contact_form_introduction';
            $static_array[] = 'contact_address_title';
            $static_array[] = 'contact_address_content';
            $static_array[] = 'contact_email_title';
            $static_array[] = 'contact_email_content';
            $static_array[] = 'contact_phone_title';
            $static_array[] = 'contact_phone_content';


        }
        $obj = array();
        foreach ($static_array as $field) {
            $obj[$field] = $settings->$field ?? "";
        }

        $final_array = array_merge($obj, array(
            'user' => $user,
            'subscribed_sources' => $user_sources,
            'server_timestamp' =>  $date->timestamp,
            'categories' => $categories,
            'menus' => $menus,
            'ads' => $ads,
            'countries' => $countries,
            'gold_prices' => array_values(array_filter($this->getGoldRateData(), function ($item) {
                return $item['ticker'] == true;
            })),
            'currency_rates' => array_filter($this->getCurrencyData(), function ($item) {
                return $item['ticker'] === true;
            }),
            'site_date' => $date->format('Y F d') . ' ' . $hijri_date . ' ' . $date->englishDayOfWeek
        ));

        return $this->returnResponse(200, 'success', $final_array, 'success');
    }


    public function getHomePage()
    {
        $user = auth('api')->check() ? auth('api')->user() : null;
        $final_array = array();
        $ticker = array();
        $featured = array();
        $videos = array();
        $categories = array();
        $countryCode = request()->input('country', 'EG');
        $country = Country::where('country_code', $countryCode)->first();
        $firstCategory = Category::where('order', 1)->first();
        $featured = News::with(['category'])->select(
            "id",
            'name',
            'slug',
            'image',
            'category_id',
            'urgent',
            'date',
            'video',
            'source_id',
            'source_link',
            'excerpt',
            'created_at'
        )->where('category_id', $firstCategory->id)
        ->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY date DESC, created_at DESC) as source_rank')
        ->orderBy('source_rank', 'asc') // Interleave: Rank 1s first, then Rank 2s...
        ->orderBy('date', 'desc')
        ->orderBy('created_at', 'desc')->paginate(12);

        $featuredIds = $featured->pluck('id');

        $videos = Video::select('id','name', 'source_id', 'video', 'image')->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY id DESC) as source_rank')
        ->orderBy('source_rank', 'asc')->orderBy('created_at', 'desc')->take(4)->get();
        $topNews = News::with(['category'])->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY id DESC) as source_rank')->latest()->take(4)->get();
        $ads = AdminAd::where('type', 'column')->take(4)->inRandomOrder()->get();
        $ads = AdminAd::where('type', 'column')->take(4)->inRandomOrder()->get();
        $fullAds = $this->getFullAdsForLocation('Home');
        $affiliates = Affiliate::with('country')->take(4)->get();

        if($user){
            $featured_categories = array();
            $subscribed_categories = $user->categories()->pluck('categories.id')->toArray();
            if(!empty($subscribed_categories)){
                $featured_categories = Category::whereIn('id', $subscribed_categories)->orderBy('order', 'asc')->get();
            }
        } else {
            $featured_categories = Category::orderBy('order', 'asc')->where('featured', 1)->get();
        }

        foreach ($featured_categories as $category) {
           
            if ($category) {
                $categories[] = array(
                    'category_name' => $category->arabic_name,
                    'category_slug' => $category->slug,
                    'category_color' => $category->color,
                    'category_id' => $category->id,
                    'news' => News::with(['category'])->distinct('source_id')->where('category_id', $category->id)
                    ->whereNotIn('id', $featuredIds)->select(
                        "id",
                        'name',
                        'slug',
                        'excerpt',
                        'image',
                        'category_id',
                        'source_id',
                        'date',
                        'urgent',
                        'video',
                        'source_link',
                        'created_at'
                    )->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY date DESC, created_at DESC) as source_rank')
                    ->orderBy('source_rank', 'asc')->orderBy('date', 'desc')->orderBy('created_at', 'desc')->skip(0)->limit(10)->get()
                );
            }
        }

        $final_array = array(
            'featured' => $featured,
            'videos' => $videos,
            'featured_categories' => $categories,
            'top_news' => $topNews,
            'affiliates' => $affiliates,
            'ads' =>  $ads,
            'full_ads' =>  $fullAds,
            'auth' => request()->header('X-User-ID'),
            'user' => $user,
        );
        return $this->returnResponse(200, 'success', $final_array, 'success');
    }

    private function getFullAdsForLocation($locationName, $categoryId = null)
    {
        $location = \App\Models\Location::where('name', $locationName)->first();
        
        // If location doesn't exist, fall back to generic "random full ads" or empty?
        // Let's fallback to just random behavior if location is missing to avoid breaking API
        if (!$location) {
             // Or maybe we treat it as "No ads for this specific location"?
             // Existing behavior was: AdminAd::where('type', 'full')->take(4)...
             // Let's stick to existing behavior as fallback if location name is wrong, but scoped to 'full'
             return AdminAd::where('type', 'full')->inRandomOrder()->take(4)->get();
        }

        $query = AdminAd::where('type', 'full')
            ->whereHas('locations', function($q) use ($location) {
                $q->where('locations.id', $location->id);
            });

        if ($categoryId) {
             $query->whereHas('categories', function($q) use ($categoryId) {
                 $q->where('categories.id', $categoryId);
             });
        }
        
        return $query->inRandomOrder()->take(4)->get();
    }

    public function getGoldRate()
    {
        try {
            $goldKeywords = ['ذهب', 'أسعار الذهب', 'سعر الذهب', 'الذهب اليوم'];

            $featured = News::with(['category'])
                ->select([
                    'id',
                    'name',
                    'slug',
                    'image',
                    'category_id',
                    'urgent',
                    'date',
                    'video',
                    'source_id',
                    'source_link',
                    'excerpt',
                    'content',
                    'created_at'
                ])
                ->where(function ($query) use ($goldKeywords) {
                    foreach ($goldKeywords as $keyword) {
                        $query->orWhere('name', 'like', "%{$keyword}%")
                            ->orWhere('excerpt', 'like', "%{$keyword}%")
                            ->orWhere('content', 'like', "%{$keyword}%");
                    }
                })
                ->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY date DESC, created_at DESC) as source_rank')
                ->orderBy('source_rank', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $ads = AdminAd::where('type', 'column')->take(4)->inRandomOrder()->get();
            $fullAds = $this->getFullAdsForLocation('Gold');
            $products = Affiliate::select('id', 'image', 'name', 'description', 'price')->orderBy('id', 'desc')->take(4)->get();




            $goldRates = $this->getGoldRateData();
            return $this->returnResponse(200, 'success', [
                'gold_rates' => $goldRates,
                'featured_news' => $featured,
                'affiliates' => $products,
                'full_ads' => $fullAds,
                'ads' => $ads,
            ], 'success');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function getCurrencyData()
    {
        try {
            $countryCode = request()->input('country', 'EG');
            $country = Country::where('country_code', $countryCode)->first();


            $baseCurrency = $country->currency_code ?? 'EGP';

            $exchangeRateCacheKey = "exchange_rate_{$baseCurrency}";

            if (Cache::has($exchangeRateCacheKey)) {
                $exchangeRateResponse = Cache::get($exchangeRateCacheKey);
            } else {
                $exchangeRateResponse = Cache::remember($exchangeRateCacheKey, now()->addHours(12), function () use ($baseCurrency) {
                    $exchangeRateUrl = "https://api.exchangerate-api.com/v4/latest/{$baseCurrency}";
                    return Http::get($exchangeRateUrl)->json();
                });
            }

            $exchangeRates = $exchangeRateResponse['rates'];

            $currenciesToConvert = [
                'USD' => [
                    'name' => 'United States Dollar',
                    'arabic_name' => 'دولار أمريكي',
                    'country_code' => 'us',
                    'ticker' => true
                ],
                'EUR' => [
                    'name' => 'Euro',
                    'arabic_name' => 'يورو',
                    'country_code' => 'eu',
                    'ticker' => true
                ],
                'SAR' => [
                    'name' => 'Saudi Riyal',
                    'arabic_name' => 'ريال سعودي',
                    'country_code' => 'sa',
                    'ticker' => true
                ],
                'AED' => [
                    'name' => 'Emirati Dirham',
                    'arabic_name' => 'درهم إماراتي',
                    'country_code' => 'ae',
                    'ticker' => false
                ],
                'BHD' => [
                    'name' => 'Bahraini Dinar',
                    'arabic_name' => 'دينار بحريني',
                    'country_code' => 'bh',
                    'ticker' => false
                ],
                'CHF' => [
                    'name' => 'Swiss Franc',
                    'arabic_name' => 'فرنك سويسري',
                    'country_code' => 'ch',
                    'ticker' => false
                ],
                'GBP' => [
                    'name' => 'Pound Sterling',
                    'arabic_name' => 'جنيه إسترليني',
                    'country_code' => 'gb',
                    'ticker' => false
                ],
                'JOD' => [
                    'name' => 'Jordanian Dinar',
                    'arabic_name' => 'دينار أردني',
                    'country_code' => 'jo',
                    'ticker' => false
                ],
                'JPY' => [
                    'name' => 'Japanese Yen',
                    'arabic_name' => 'ين ياباني',
                    'country_code' => 'jp',
                    'ticker' => false
                ],
                'KWD' => [
                    'name' => 'Kuwaiti Dinar',
                    'arabic_name' => 'دينار كويتي',
                    'country_code' => 'kw',
                    'ticker' => false
                ],
                'OMR' => [
                    'name' => 'Omani Rial',
                    'arabic_name' => 'ريال عماني',
                    'country_code' => 'om',
                    'ticker' => false
                ],
                'QAR' => [
                    'name' => 'Qatari Riyal',
                    'arabic_name' => 'ريال قطري',
                    'country_code' => 'qa',
                    'ticker' => false
                ],
            ];

            $data = [];

            foreach ($currenciesToConvert as $currencyCode => $currencydata) {
                $exchangeRate = isset($exchangeRates[$currencyCode]) ? round(1 / $exchangeRates[$currencyCode], 2) : null;

                $data[] = [
                    'name' => $currencydata['name'],
                    'arabic_name' => $currencydata['arabic_name'],
                    'country_code' => $currencydata['country_code'],
                    'currency_code' => $currencyCode,
                    'exchange_rate' => $exchangeRate ? "{$exchangeRate} {$baseCurrency}" : null,
                    'value' => $exchangeRate,
                    'ticker' => $currencydata['ticker']
                ];
            }

            return $data;
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getGoldRateData()
    {
        try {

            $countryCode = request()->input('country', 'EG');
            $country = Country::where('country_code', $countryCode)->first();
            $baseCurrency = $country->currency_code ?? 'EGP';

            $cacheKey = "gold_rates_{$baseCurrency}";
            $cacheDuration = now()->addHours(12);

            // Check if data exists in cache
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Fetch gold prices from goldapi.io
            $goldUrl = env('GOLD_RATE_PROXY_URL', '') . "?currency={$baseCurrency}";
            $goldResponse = Http::get($goldUrl);
            $response = $goldResponse;

            if (!$response->successful()) {
                \Log::error('GoldAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return [];
            }

            $apiData = $response->json();
            $pricePerOunce = $apiData['data']['items'][0]['xauPrice'] ?? 0;
            $pricePerGram = $pricePerOunce / 31.1035; 

            

            // Get currency info
            $currencyInfo = $country;
            $currencyCode = $country->arabic_currency_name ?? 'EGP';

            // Define Karats
            $karats = [
                '24k' => ['ratio' => 1, 'ticker' => true],
                '22k' => ['ratio' => 22/24, 'ticker' => false],
                '21k' => ['ratio' => 21/24, 'ticker' => true],
                '20k' => ['ratio' => 20/24, 'ticker' => false],
                '18k' => ['ratio' => 18/24, 'ticker' => true],
                '16k' => ['ratio' => 16/24, 'ticker' => false],
                '14k' => ['ratio' => 14/24, 'ticker' => false],
                '10k' => ['ratio' => 10/24, 'ticker' => false],
            ];

            $goldRates = [];
            foreach ($karats as $k => $data) {
                $price =  $pricePerGram * $data['ratio'];
                $price = number_format($price, 2);
                $goldRates[] = [
                    'name' => "price_gram_{$k}",
                    'arabic_name' => "ذهب عيار " . str_replace('k', '', $k),
                    'price' => $price,
                    'exchange_rate' => "{$currencyCode} {$price}",
                    'ticker' => $data['ticker']
                ];
            }

            // Add Ounce price
            $goldRates[] = [
                'name' => 'per_ounce',
                'arabic_name' => 'أونصة ذهب',
                'price' => number_format($pricePerOunce, 2),
                'ticker' => false
            ];

            // Cache the results
            Cache::put($cacheKey, $goldRates, $cacheDuration);

            return $goldRates;

        } catch (\Exception $e) {
            \Log::error('Error fetching gold rates', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }


    public function getExchangeRates()
    {
        try {
            $country = request()->input('country', 'مصر');
            $countryToCurrency = [
                'الإمارات العربية المتحدة' => ['code' => 'AED', 'name' => 'United Arab Emirates Dirham'],
                'قطر' => ['code' => 'QAR', 'name' => 'Qatari Riyal'],
                'مصر' => ['code' => 'EGP', 'name' => 'Egyptian Pound'],
                'المملكة العربية السعودية' => ['code' => 'SAR', 'name' => 'Saudi Riyal'],
            ];

            $currencyKeywords = ['السعر', 'العملة', 'تحويل العملات',];

            $featured = News::with(['category'])
                ->select([
                    'id',
                    'name',
                    'slug',
                    'content',
                    'image',
                    'category_id',
                    'urgent',
                    'date',
                    'video',
                    'source_id',
                    'source_link',
                    'excerpt',
                    'created_at'
                ])
                ->where(function ($query) use ($currencyKeywords) {
                    foreach ($currencyKeywords as $keyword) {
                        $query->orWhere('name', 'like', "%{$keyword}%")
                            ->orWhere('excerpt', 'like', "%{$keyword}%")
                            ->orWhere('content', 'like', "%{$keyword}%");
                    }
                })
                ->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY date DESC, created_at DESC) as source_rank')
                ->orderBy('source_rank', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $featured->each(function ($news) {
                $news->related_news = $news->related_news;
            });

            $ads = AdminAd::where('type', 'column')->take(4)->inRandomOrder()->get();
            $fullAds = $this->getFullAdsForLocation('Exchange Rate');
            $products = Affiliate::select('id', 'image', 'name', 'description', 'price')->orderBy('id', 'desc')->take(4)->get();



            return $this->returnResponse(200, 'success', [
                'exchange_rates' => $this->getCurrencyData(),
                'featured_news' => $featured,
                'affiliates' => $products,
                'full_ads' => $fullAds,
                'ads' => $ads,
            ], 'success');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCategoryPage()
    {
        $page = request()->page;
        $category_slug = request()->category;
        $news = array();
        if (!empty($category_slug)) {
            $category = Category::where('slug', $category_slug)->firstOrFail();
            $categoryName = $category->arabic_name;
            $news = [];
            $final_array = array();
            $categories = array();
            $ads = $category->ads()->where('type', 'column')->take(4)->inRandomOrder()->get();
            $ads = $category->ads()->where('type', 'column')->take(4)->inRandomOrder()->get();
            // Use helper to get ads for 'Category' location AND this specific category
            $fullAds = $this->getFullAdsForLocation('Category', $category->id);
            $affiliates = $category->affiliates()->take(4)->get();

            $tags = $category->keywords()
                ->with('countries')
                ->select('id', 'keyword_name','short_description', 'description', 'image')
                ->orderBy('id', 'desc')
                ->get();
                
            $news = News::with(['category'])->where('category_id', $category->id)
                ->select(
                    "id",
                    'name',
                    'slug',
                    'image',
                    'category_id',
                    'source_id',
                    'date',
                    'urgent',
                    'video',
                    'source_link',
                    'created_at'
                )
                ->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY date DESC, created_at DESC) as source_rank')
                ->orderBy('source_rank', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')->paginate(15);

            if($news->count() == 0){
                $news = News::with(['category'])
                ->where(function($q) use ($categoryName) {
                            $q->where('name', 'LIKE', "%{$categoryName}%")
                            ->orWhere('excerpt', 'LIKE', "%{$categoryName}%")
                            ->orWhere('content', 'LIKE', "%{$categoryName}%");
                })
                ->select(
                    "id",
                    'name',
                    'slug',
                    'image',
                    'category_id',
                    'source_id',
                    'date',
                    'urgent',
                    'video',
                    'source_link',
                    'created_at'
                )
                ->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY date DESC, created_at DESC) as source_rank')
                ->orderBy('source_rank', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')->paginate(15);
            }

            if ($page) {
                $final_array = array(
                    'news' => $news,
                );
            } else {
                $final_array = array(
                    'category' => $category,
                    'tags' => $tags,
                    'news' => $news,
                    'affiliates' => $affiliates,
                    'ads' =>  $ads,
                    'full_ads' =>  $fullAds,
                );
            }

            return $this->returnResponse(200, 'success', $final_array, 'success');
        } else {
            return $this->returnResponse(403, 'failure', $news, 'not found');
        }
    }

    public function getNewsSourcePage()
    {
        $page = request()->page;
        $source_slug = request()->source;
        $source = Source::where('arabic_name', $source_slug)->first();
        $news = [];
        if (!empty($source_slug)) {
            $final_array = array();
            if (!empty($source_slug)) {
                $news = News::with(['category'])->where('source_id', $source->id)->select(
                    "id",
                    'name',
                    'slug',
                    'image',
                    'category_id',
                    'date',
                    'urgent',
                    'video',
                    'source_id',
                    'source_link',
                    'created_at'
                )
                ->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY date DESC, created_at DESC) as source_rank')
                ->orderBy('source_rank', 'asc')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')->take(20)->paginate(20);
            }

                $final_array = array(
                    'news' => $news,
                    'affiliates' => Affiliate::select('id', 'image', 'name', 'description', 'price')->orderBy('id', 'desc')->take(4)->get(),
                    'full_ads' => $this->getFullAdsForLocation('Source'),
                    'source' => $source->arabic_name,
                    'source_icon' => $source->logo_url,
                    'source_follower' => $source->followers,
                    'source_description' => $source->description,
                    'source_social_links' => $source->social_links,
                    'source_url'   => $source->website
                );

            return $this->returnResponse(200, 'success', $final_array, 'success');
        } else {
            return $this->returnResponse(403, 'failure', $news, 'not found');
        }
    }

    public function getCoverageNews(){
        $news_id = request()->news_id;
        $coverage_news = collect();
        
        if (!empty($news_id)) {
            $news = News::where('id', $news_id)->first();
            $country_id = $news->sources->country_id;
            if($news){
                $coverage_news = News::search($news->name)->where('category_id', $news->category_id)
                ->where('country_id', $country_id)
                ->where('id', '!=', $news->id) // Exclude current news
                ->orderBy('created_at', 'desc')->take(3)->get();
                
                $final_array = array(
                    'coverage_news' => $coverage_news,
                );
            }
        }
        
        return $this->returnResponse(200, 'success', $final_array, 'success');
    }

    /**
     * Search for news items that contain a specific number of keywords
     * 
     * @param array $keywords - Array of keywords to search
     * @param int $minMatches - Minimum number of keywords that must match
     * @param int $excludeId - News ID to exclude from results
     * @param int $limit - Maximum number of results to return
     * @return Collection
     */
    /*
    private function searchByKeywordCount($keywords, $minMatches, $excludeId, $limit)
    {
        $results = News::with(['category'])
            ->whereNotIn('id', [$excludeId])
            ->where(function($query) use ($keywords, $minMatches) {
                // For each possible combination of keywords
                if ($minMatches == 3 && count($keywords) >= 3) {
                    // All 3 keywords must be present
                    foreach ($keywords as $keyword) {
                        $query->where(function($q) use ($keyword) {
                            $q->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('excerpt', 'LIKE', "%{$keyword}%")
                            ->orWhere('content', 'LIKE', "%{$keyword}%");
                        });
                    }
                } elseif ($minMatches == 2 && count($keywords) >= 2) {
                    // At least 2 keywords must be present
                    $matchCount = 0;
                    foreach ($keywords as $keyword) {
                        $query->orWhere(function($q) use ($keyword) {
                            $q->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('excerpt', 'LIKE', "%{$keyword}%")
                            ->orWhere('content', 'LIKE', "%{$keyword}%");
                        });
                    }
                } elseif ($minMatches == 1) {
                    // At least 1 keyword must be present
                    foreach ($keywords as $keyword) {
                        $query->orWhere(function($q) use ($keyword) {
                            $q->where('name', 'LIKE', "%{$keyword}%")
                            ->orWhere('excerpt', 'LIKE', "%{$keyword}%")
                            ->orWhere('content', 'LIKE', "%{$keyword}%");
                        });
                    }
                }
            })
            ->select(
                "id", 'name', 'slug', 'image', 'category_id', 
                'date', 'urgent', 'video', 'source_id', 
                'source_link', 'created_at'
            );
        
        // For 2 keyword matching, we need to filter results manually
        if ($minMatches == 2) {
            $results = $results->get()->filter(function($item) use ($keywords) {
                $matchCount = 0;
                foreach ($keywords as $keyword) {
                    $content = $item->name . ' ' . $item->excerpt . ' ' . $item->content;
                    if (mb_stripos($content, $keyword) !== false) {
                        $matchCount++;
                    }
                }
                return $matchCount >= 2;
            })->take($limit);
        } else {
            $results = $results->take($limit)->get();
        }
        
        return $results;
    }
    */

    public function getSingleNewsPage()
    {
        $news_slug = request()->slug;
        $news_id = request()->id;
        $news = null;
        if (!empty($news_slug) ||  !empty($news_id)) {

            if (!empty($news_slug) && $news_slug) {
                $news = News::with(['category'])->where('slug', $news_slug)->firstOrFail();
            }

            if (!empty($news_id) && $news_id) {
                $news = News::with(['category'])->where('id', $news_id)->firstOrFail();
            }


            if(request()->country){
                $country = Country::where('country_code', request()->country)->first();
                $countryId = $country->id;
            }else{
                $countryId = $news->sources->countries[0]->id;
            }

            $keywords = $news->keywords;
            $previous_news_id = News::where('id', '<', $news->id)->whereHas('sources.countries', function ($q) use ($countryId) {
                $q->where('countries.id', $countryId);
            })->max('id');
            $firstCategory = Category::where('order', 1)->first();
            $topNews = News::with(['category'])->select(
                "id",
                'name',
                'slug',
                'image',
                'category_id',
                'urgent',
                'date',
                'video',
                'source_id',
                'source_link',
                'excerpt',
                'created_at'
            )->where('category_id', $firstCategory->id)
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY source_id ORDER BY id DESC) as source_rank')
            ->orderBy('source_rank', 'asc') // Interleave: Rank 1s first, then Rank 2s...
            ->orderBy('created_at', 'desc')->take(4)->get();
            $post_related_news = News::with(['category'])->where('category_id', $news->category_id)->select(
                "id",
                'name',
                'slug',
                'image',
                'category_id',
                'date',
                'urgent',
                'video',
                'source_id',
                'source_link',
                'created_at'
            )->whereNotIn('id', [$previous_news_id, $news->id])->inRandomOrder()->take(3)->get()->toArray();
            $related_news = News::with(['category'])->where('category_id', $news->category_id)->select(
                "id",
                'name',
                'slug',
                'image',
                'category_id',
                'date',
                'urgent',
                'video',
                'source_id',
                'source_link',
                'created_at'
            )->whereNotIn('id', [$previous_news_id, $news->id])->inRandomOrder()->take(15)->get()->toArray();

            $related_news = Arr::shuffle($related_news);
            $html = $news->content;
            $crawler = new Crawler($html);
            
            $crawler->filter('a')->each(function($node){
                $domNode = $node->getNode(0);

                if ($domNode && $domNode->parentNode && @$domNode->parentNode->firstChild->firstChild->nodeName == 'img') {
                    $domNode->parentNode->removeChild($domNode);
                }
            });

            $crawler->filter('img, figure, script, style, noscript, .twitter-tweet, .entry-tags, .internal-pages-content-tools-cont, .list-tags, #lightgallery,script, img, ins, .tagcloud, .addthis_toolbox, .article-actions-secondary, #MPU, #semat, #comments-block, .lightgallery-element, .instagram-media')->each(function ($crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            });

            $elements = $crawler->each(function ($node) {
                return $node->html();
            });

            $category = Category::where('id', $news->category_id)->firstOrFail();
            $affiliates = $category->affiliates()
                ->select('affiliates.id', 'affiliates.name', 'affiliates.url', 'affiliates.price', 'affiliates.image')
                ->inRandomOrder()
                ->take(4)
                ->get();

            //Log::info('Has Content Class '.$content_classes);
            $html = implode(" ", $elements);
            $html = str_replace("<body>", "", $html);
            $html = str_replace("</body>", "", $html);

            $news->content = $html;
            if (!empty($news->image)) {
                //$news->image_size = getimagesize($news->image);
            }

            $news->related_news = $post_related_news;

            $final_array = array(
                'news' => $news,
                'related_news' => $related_news,
                'previous_news_id' => $previous_news_id,
                'keyowrds' => $keywords,
                'affiliates' => $affiliates,
                'top_news' => $topNews
            );

            return $this->returnResponse(200, 'success', $final_array, 'success');
        } else {
            return $this->returnResponse(403, 'failure', $news, 'not found');
        }
    }

    public function getSearchPage()
    {
        $keyword = request()->keyword;
        $source = request()->source;
        $category = request()->category;
        $type = request()->type;
        $ads = AdminAd::where('type', 'column')->whereHas('locations', function ($query) {
            $query->where('name', 'Search');
        })->take(4)->inRandomOrder()->get();
        $fullAds = $this->getFullAdsForLocation('Search');

        $news = News::with(['category'])
            ->select(
                "id",
                'name',
                'slug',
                'content',
                'image',
                'category_id',
                'date',
                'views',
                'shares',
                'urgent',
                'video',
                'source_id',
                'source_link',
                'created_at'
            )
            ->orderBy('id', 'DESC');

        if (!empty($source)) {
            $news->where('source', $source);
        }

        if (!empty($category)) {
            $news->where('category_id', $category);
        }

        if (!empty($keyword)) {
            $terms = explode(' ', $keyword);

            if ($type == 'exact') {
                // Logic for exact match of the word
                $news->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('content', 'LIKE', '%' . $keyword . '%');
                });
            } elseif ($type == 'any') {
                $news->where(function ($q) use ($terms) {
                    foreach ($terms as $term) {
                        $q->orWhere('name', 'LIKE', '%' . $term . '%')
                            ->orWhere('content', 'LIKE', '%' . $term . '%');
                    }
                });
            } else {
                // Logic for all of the words
                foreach ($terms as $term) {
                    $news->where(function ($q) use ($term) {
                        $q->where('name', 'LIKE', '%' . $term . '%')
                            ->orWhere('content', 'LIKE', '%' . $term . '%');
                    });
                }
            }
        }

        $news = $news->distinct('slug')->take(40)->paginate(40);
        $finalArray = [
            'news' => $news,
            'ads' => $ads,
            'full_ads' => $fullAds,
            'affiliates' => Affiliate::select('id', 'image', 'name', 'description', 'price')->orderBy('id', 'desc')->take(4)->get(),
        ];


        if (!empty($news)) {
            return $this->returnResponse(200, 'success', $finalArray, 'found');
        } else {
            return $this->returnResponse(403, 'failure', $news, 'not found');
        }
    }



    public function subscribeToNewsletter()
    {
        $email = request()->email;
        $userId = (!empty($_POST['userId'])) ? $_POST['userId'] : '';

        if (!$email) {
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

        // Use database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Save to local database first
            $newsletter = new Newsletter;
            $newsletter->newsletter_email = $email;
            $newsletter->newsletter_user_id = (!empty($userId)) ? $userId : 0;
            $newsletter->save();

            $message = ($this->app_lang == "ar") ? " تم الاشتراك بنجاح" : "You have been subscribed successfully";
            return $this->returnResponse(200, 'success', 1, $message);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Newsletter subscription failed', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $message = ($this->app_lang == "ar") ? "حدث خطأ أثناء الاشتراك" : "An error occurred during subscription";
            return $this->returnResponse(500, 'failure', 0, $message);
        }
    }


        public function getMainSources()
        {
            try {
                $countryCode = request()->input('country', 'EG');
                $country = Country::where('country_code', $countryCode)->first();
                
                $newsSources = Source::get();
                $sources = [];
                foreach ($newsSources as $item) {
                    $sources[] = [
                        'id' => $item->id,
                        'name' => $item->arabic_name,
                        'image' => $item->logo_url ?? null,
                        'subscribe' => $item->subscribe,
                    ];
                }
                
                return $this->returnResponse(200, 'success', $sources, 'found');
            } catch (\Exception $e) {
                return $this->returnResponse(500, 'error', [], 'An error occurred: ' . $e->getMessage());
            }
        }


    // Old Function getMainCategories() ==============>

    // public function getMainCategories()
    // {
    //     $catId = request()->catId;
    //     $categories = Category::select("id as category_id", 'name', 'order','slug','color')
    //         ->where("parent_id", 0)->orderBy("order", 'asc');
    //     if (!empty($catId)) {
    //         $categories = $categories->where('id', $catId);
    //     }
    //     $categories = $categories->get();
    //     foreach ($categories as $category) {
    //         $category->subs = $this->getSubCategories($category->category_id);
    //     }
    //     $this->table_prefix = "";
    //     $categories = $this->removeMeta($categories->toArray());
    //     return $this->returnResponse(200, 'success', $categories, 'found');
    // }

    public function getMainCategories()
    {
        $catId = request()->catId;
        $categories = Category::select("id as category_id", 'name', 'order', 'slug', 'color')
            ->orderBy("order", 'asc');

        if (!empty($catId)) {
            $categories = $categories->where('id', $catId);
        }

        $categories = $categories->get();
        // dd($categories);

        $this->table_prefix = "";
        $categories = $categories->toArray();
        return $this->returnResponse(200, 'success', $categories, 'found');
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
        $terms = Page::where('page_slug', $slug)->firstOrFail();
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
            return response()->json(['status' => 'error', 'message' => 'Email is required']);
        }
        $advertiser = Advertiser::where('adv_email', $email)->first();
        if (empty($advertiser)) {
            return response()->json(['status' => 'error', 'message' => 'User does not exist']);
        }
        $advertiser->adv_forgot_password_code = generateRandomInteger(4);
        $advertiser->adv_reset_token =  base64_encode($email) . '_' . generateRandomInteger(15);
        $advertiser->adv_verify_token =  base64_encode($email) . '_' . generateRandomInteger(15);
        $advertiser->save();

        try {
            Mail::to($email)->send(new ConfirmationCodeMail($advertiser->adv_forgot_password_code, $advertiser->adv_verify_token));
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'Failed to send confirmation email. Reason: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'message' => 'A reset password link has been sent to your email',
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request = $request->all();
        $code = (!empty($request['code'])) ? $request['code'] : '';
        $reset_token = (!empty($request['token'])) ? $request['token'] : '';

        $advertiser = Advertiser::where('adv_verify_token', $request['token'])->first();
        if (empty($advertiser)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }

        $arr = [
            'name' => $advertiser->adv_first_name . ' ' . $advertiser->adv_last_name,
            'email' => $advertiser->adv_email,
            'redirect_path' =>  '/auth/update-password?token=' . $advertiser->adv_reset_token
        ];
        return $this->returnResponse(200, 'success', $arr, 'تم تعديل كلمة المرور بنجاح');
    }

    public function resetPassword(Request $request)
    {
        $request = $request->all();
        $token = (!empty($request['token'])) ? $request['token'] : '';
        $password = (!empty($request['password'])) ? $request['password'] : '';
        if (empty($token) || empty($password)) {
            return $this->missingParameter();
        }
        $advertiser = Advertiser::where('adv_reset_token', $token)->first();
        if (empty($advertiser)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        if (strlen($password) < 8) {
            $message = ($this->app_lang == "ar") ? " كلمة المرور يجب الا تقل عن 8 خانات" : "Email is already used";
            return $this->returnResponse(403, 'failure', 0, $message);
        }

        $advertiser->adv_forgot_password_code = NULL;
        $advertiser->adv_reset_token = NULL;
        $advertiser->adv_verify_token = NULL;
        $advertiser->adv_password = Hash::make($password);
        $advertiser->save();
        return $this->returnResponse(200, 'success', 1, 'تم تعديل كلمة المرور بنجاح');
    }

    /*
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
    */

    // public function getCategoriesWithNews()
    // {
    //     $categories = Category::select("id as category_id", 'name', 'order')->orderBy("order", 'asc');
    //     $categories = $categories->get();
    //     $cats = array();
    //     foreach ($categories as $category) {
    //         $cat = $this->getCategoryNews($category);
    //         if (!empty($cat['news'])) $cats[] = $cat;
    //     }
    //     // $this->table_prefix = "";
    //     // $categories = $this->removeMeta($categories->toArray());
    //     return $cats;
    // }

    // public function getCategoryNews($category)
    // {
    //     $news = News::select(
    //         "id",
    //         'name',
    //         'image',
    //         'category_id',
    //         'urgent',
    //         'video',
    //         'source_id',
    //         'source_link',
    //         'created_at'
    //     )->where('category_id', $category->category_id)->limit(3)->get();
    //     foreach ($news as $newsItem) {
    //         $newsItem->news_video = (!empty($newsItem->news_video)) ? 'https://www.youtube.com/embed/' . getYoutubeId($newsItem->news_video) : '';
    //         if (!empty($fav_user)) {
    //             $oldFavorite = Favorite::where('fav_news_id', $newsItem->id)
    //                 ->where('fav_user_id', $fav_user)->first();
    //             if (!empty($oldFavorite)) {
    //                 $newsItem->is_favorite = 1;
    //             } else {
    //                 $newsItem->is_favorite = 0;
    //             }
    //         } else {
    //             $newsItem->is_favorite = 0;
    //             $fav_user = '';
    //         }
    //         $newsItem->relatedNews = $this->getRelatedNews($newsItem, $fav_user);
    //         $newsItem->newsKeywords = getNewsKeywordsWithName($newsItem->id);
    //     }
    //     $this->table_prefix = "news_";
    //     $news = $this->removeMeta($news->toArray());
    //     $cat = array();
    //     $cat['name'] = $category->name;
    //     $cat['news'] = $news;
    //     return $cat;
    // }


    // public function getAdsense(Request $request)
    // {
    //     $area = $request->area;
    //     $adsense = Adsense::select("id", 'adsense_name', 'adsense_code', 'adsense_area');
    //     if (isset($area)) {
    //         $adsense = $adsense->where('adsense_area', $area);
    //     }
    //     $adsense = $adsense->get();
    //     $this->table_prefix = "adsense_";
    //     $adsense = $this->removeMeta($adsense->toArray());
    //     return $this->returnResponse(200, 'success', $adsense, 'found');
    // }

    /*
    public function getAllNews(Request $request) {}

    public function getNews(Request $request, $userId)
    {
        if (!$userId) {
            return $this->returnResponse(200, 'success', null, 'found');
        }

        $ads = AdminAd::getAllAds();
        $fav_user = $request->favUser;
        $cat = $request->cat;
        $newsId = $request->newsId;
        $subCat = $request->subCat;
        $source = $request->source;
        $tags = $request->tags;
        $keyword = $request->keyword;
        $mostViewed = $request->mostViewed;
        $favUserId = $userId;
        $news = News::select(
            "id",
            'name',
            'content',
            'image',
            'category_id',
            'views',
            'shares',
            'urgent',
            'video',
            'source_id',
            'source_link',
            'created_at'
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
                })->orWhereIn(function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                });
        }
        if (isset($source)) {
            $news = $news->where('source', $source);
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
            $news = $news->orderBy('views', 'Desc')->limit(3);
        } else {
            $news = $news->orderBy('id', 'Desc');
        }

        $news = $news->paginate(20);
        foreach ($news as $newsItem) {
            $newsItem->news_video = (!empty($newsItem->news_video)) ? 'https://www.youtube.com/embed/' . getYoutubeId($newsItem->news_video) : '';
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
            $newsItem->related_news = $this->getRelatedNews($newsItem, $fav_user);
            $newsItem->newsKeywords = getNewsKeywordsWithName($newsItem->id);
            $newsItem->previousNews = $this->getPreviousNews($newsItem, $fav_user);
            $newsItem->otherCat = $this->allnews_categories($newsItem);
            $newsItem->single_ad = $ads['single_ad'];
            $newsItem->affiliates = $ads['affiliates'];
        }
        $this->table_prefix = "news_";
        //        $news = $this->removeMeta($news->toArray());
        return $this->returnResponse(200, 'success', $news, 'found');
    }


    public function homepage()
    {

        $home = [];
        $home['tags'] = $this->homepage_tags();
        return $this->returnResponse(200, 'success', $home, 'found');
    }

    public function getPreviousNews($newsItem, $fav_user = '')
    {

        $fav_user = $fav_user;
        $news = News::select(
            "id",
            'name',
            'image',
            'source_id',
            'source_link',
            'created_at'
        );
        $previous_id = $news->where('id', '<', $newsItem->id)->max('id');
        $news = $news->where('id', '=', $previous_id)->first();

        return $news;
    }

    public function allnews_categories($newsItem)
    {

        $news =  DB::table("news_categories")->where('id', $newsItem->id)->get();
        $categories = [];

        foreach ($news as $news_name) {

            $name = \App\Models\Category::where('id', $news_name->category_id)->first();
            if (!empty($name)) {
                array_push($categories, ['category_id' => $name->id, 'name' => $name->name]);
            }
        }

        return   array_unique($categories, SORT_REGULAR);
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
        $news = News::select(
            "id",
            'name',
            'content',
            'image',
            'category_id',
            'views',
            'shares',
            'urgent',
            'video',
            'source_id',
            'source_link',
            'created_at'
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
                })->orWhereIn(function ($query) use ($keyword) {
                    $query->select('id')
                        ->from(with(new Category)->getTable())
                        ->where('name', 'LIKE', '%' . $keyword . '%')
                        ->distinct('id')->pluck('id')->toArray();
                });
        }
        if (isset($subCat)) {
            $news = $news->where($subCat);
        }
        if (isset($source)) {
            $news = $news->where('source', $source);
        }
        if (isset($mostViewed)) {
            $news = $news->orderBy('views', 'Desc')->limit(3);
        } else {
            $news = $news->orderBy('id', 'Desc');
        }
        $news = $news->limit(5);
        $news = $news->get();
        foreach ($news as $newsItem) {
            $newsItem->news_video = (!empty($newsItem->news_video)) ? 'https://www.youtube.com/embed/' . getYoutubeId($newsItem->news_video) : '';
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
        }
        $categoriesNews = $this->getCategoriesWithNews();
        $this->table_prefix = "news_";
        $news = $this->removeMeta($news->toArray());
        // return $this->returnResponse(200,'success',$news,'found');
        $response = array("status" => 200, "sub_message" => 'success', "return" => $news, 'categories' => $categoriesNews, "message" => 'found');
        return response($response);
    }
    */

    public function getRelatedNews($newsItem, $fav_user = '')
    {
        $fav_user = $fav_user;
        $news = News::select(
            "id",
            'name',
            'image',
            'source_id',
            'source_link',
            'created_at'
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

    /*
    public function getFooterMenu()
    {
        $menus = Menu::select("id as menu_id", 'menu_name', 'menu_parent_slug', 'menu_content')
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
        $this->table_prefix = "";
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
        $settings = app(GeneralSettings::class);
        $value = $settings->$key ?? "";
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

    public function ads_areas(Request $request)
    {


        if (isset($request->news_id)) {
            $obj = [];
            $adsence = Adsense::where('id', $request->news_id)->get();
            foreach ($adsence as $key => $value) {

                $ads_areas = AdsArea::where('id', $value['adsense_area'])->first();

                $obj['adsense_name'] = $value['adsense_name'];
                $obj['adsense_area'] = $ads_areas->name;
                $obj['adsense_code'] = $value['adsense_code'];
            }

            return $this->returnResponse(200, 'success', $obj, 'found');
        } else {
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
    */






    public function addUserFavorite()
    {
        $favUserId = request()->userId;
        $newsId = request()->newsId;
        
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
            $message = ($this->app_lang == "ar") ? "الخبر غير موجود" : "News does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        
        $existingFavorite = Favorite::where('fav_news_id', $newsId)
            ->where('fav_user_id', $favUserId)
            ->first();
        
        if ($existingFavorite) {
            // Remove from favorites
            $existingFavorite->delete();
            $message = ($this->app_lang == "ar") ? "تم الحذف من المفضلة بنجاح" : "Removed from favorites successfully";
            $data = ['is_favorited' => false];
        } else {
            // Add to favorites
            $favorite = new Favorite;
            $favorite->fav_user_id = $favUserId;
            $favorite->fav_news_id = $newsId;
            $favorite->save();
            $message = ($this->app_lang == "ar") ? "تمت الإضافة للمفضلة بنجاح" : "Added to favorites successfully";
            $data = ['is_favorited' => true];
        }
        
        return $this->returnResponse(200, 'success', $data, $message);
    }


    public function updateEmailPreference()
    {
        $userId = request()->userId;
        $email_notifications_enabled = request()->email_notifications_enabled;
        
        if (empty($userId) || !isset($email_notifications_enabled)) {
            return $this->missingParameter();
        }

        $user = Advertiser::find($userId);
        if (empty($user)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }

        $user->email_notifications_enabled = $email_notifications_enabled;
        $user->save();
        
        if ($email_notifications_enabled) {
            $message = ($this->app_lang == "ar") ? "تم تفعيل الإشعارات عبر البريد الإلكتروني بنجاح" : "Email notifications enabled successfully";
        } else {
            $message = ($this->app_lang == "ar") ? "تم إلغاء تفعيل الإشعارات عبر البريد الإلكتروني بنجاح" : "Email notifications disabled successfully";
        }


        return $this->returnResponse(200, 'success', $user->email_notifications_enabled, $message);
    }


    public function updatePushPreference()
    {
        $userId = request()->userId;
        $push_notifications_enabled = request()->push_notifications_enabled;
        

        if (empty($userId) || !isset($push_notifications_enabled)) {
            return $this->missingParameter();
        }

        $user = Advertiser::find($userId);
        if (empty($user)) {
            $message = ($this->app_lang == "ar") ? "المستخدم غير موجود" : "User does not exist";
            return $this->returnResponse(403, 'failure', 0, $message);
        }

        $user->push_notifications_enabled = $push_notifications_enabled;
        $user->save();

        if ($push_notifications_enabled) {
            $message = ($this->app_lang == "ar") ? "تم تفعيل الإشعارات بنجاح" : "Notifications enabled successfully";
        } else {
            $message = ($this->app_lang == "ar") ? "تم تعطيل الإشعارات بنجاح." : "Notifications disabled successfully";
        }

        return $this->returnResponse(200, 'success', $user->push_notifications_enabled, $message);
    }

    /*
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
    */



    public function deleteUserFavorite()
    {
        $favUserId = request()->userId;
        $newsId = request()->newsId;
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
            ->where('fav_user_id', $favUserId)->first();
        if (empty($oldFavorite)) {
            $message = ($this->app_lang == "ar") ? " الخبر غير مضاف للمفضلة" : "The user does not have this brand in his favorites";
            return $this->returnResponse(403, 'failure', 0, $message);
        }
        Favorite::where('fav_news_id', $newsId)
            ->where('fav_user_id', $favUserId)->delete();
        $message = ($this->app_lang == "ar") ? "   تم الحذف بنجاح" : "Deleted successfully";

        return $this->returnResponse(200, 'success', 1, $message);
    }


    /*
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
    */

public function getUserFavorites()
{
    $userId = request()->header('X-User-ID');
    $user = Advertiser::find($userId);

    if (!$user) {
        $final_array = array(
            'news' => [],
            'auth' => request()->header('X-User-ID')
        );
        return $this->returnResponse(200, 'success', $final_array, 'success');
    }
    
    $favorites = News::with(['category'])
        ->select(
            "id",
            'name',
            'slug',
            'image',
            'category_id',
            'urgent',
            'date',
            'video',
            'source_id',
            'source_link',
            'excerpt',
            'created_at'
        )
        ->whereHas('favorites', function($query) use ($user) {
            $query->where('fav_user_id', $user->id);
        })
        ->orderBy('id', 'desc')
        ->paginate(10);
    
    $final_array = array(
        'news' => $favorites->items(),
        'pagination' => [
            'current_page' => $favorites->currentPage(),
            'last_page' => $favorites->lastPage(),
            'per_page' => $favorites->perPage(),
            'total' => $favorites->total(),
            'has_more_pages' => $favorites->hasMorePages()
        ],
        'auth' => request()->header('X-User-ID')
    );
    
    return $this->returnResponse(200, 'success', $final_array, 'success');
}


    /*
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
        $settings = app(GeneralSettings::class);
        $facebook = $settings->app_facebook ?? "";
        $twitter = $settings->app_twitter ?? "";
        $whatsapp = $settings->app_whatsapp ?? "";
        $massenger = $settings->app_massenger ?? "";

        return [
            'facebook' => $facebook,
            'twitter' => $twitter,
            'whatsapp' => $whatsapp,
            'massenger' => $massenger,
        ];
    }
    */

    public function getAllCountries()
    {
        try {
            $countries = Country::all();

            if ($countries->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No countries found',
                    'countries' => []
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Countries retrieved successfully',
                'countries' => $countries
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving countries',
                'error' => $e->getMessage(),
                'countries' => []
            ], 500);
        }
    }

    public function getLiveStreams(Request $request)
    {
        try {
            $countryCode = request()->country;
            $query = LiveStream::select('id', 'name', 'image', 'description', 'video', 'url');

            if (!empty($countryCode)) {
                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Country not found',
                        'videos' => []
                    ], 404);
                }
                $query->whereHas('countries', function ($q) use ($country) {
                    $q->where('country_id', $country->id);
                });
            }

            $liveStreams = $query->inRandomOrder()->take(50)->get();

            if ($liveStreams->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No live streams found',
                    'videos' => []
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Live streams retrieved successfully',
                'videos' => $liveStreams
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving live streams',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVideoPage()
    {
        try {
            $categorySlug = request()->category;

            $category = null;
            $tags = [];
            $videos = null;

            // Build the base query
            $query = Video::select('id','name', 'source_id', 'video', 'image');

            $categories = Category::select('name', 'arabic_name', 'order', 'slug')
            ->orderBy("order", 'asc')->get();

            // Apply category filter if provided
            if (!empty($categorySlug)) {
                $category = Category::where('slug', $categorySlug)->first();

                if (!$category) {
                    return $this->returnResponse(404, 'failure', [], 'Category not found');
                }

                $query->where('category_id', $category->id)->orderBy('id', 'desc');
            }

            $videos = $query->paginate(10);

            $finalArray = [
                'videos' => $videos,
                'categories' => $categories,
            ];

            return $this->returnResponse(200, 'success', $finalArray, 'success');
        } catch (\Exception $e) {
            dd($e->getMessage());
            \Log::error('Error in getVideoPage:', ['error' => $e->getMessage()]);
            return $this->returnResponse(500, 'failure', [], 'An error occurred while processing your request');
        }
    }


    public function getAffiliatePage()
    {
        try {
            $categorySlug = request()->category;
            $category = null;
            $products = [];
            $static_array = array(
            'banner_1',
            'banner_2',
            'banner_3',
            'banner_1_link',
            'banner_2_link',
            'banner_3_link',
            );
            $obj = array();

            $settings = app(GeneralSettings::class);
            foreach ($static_array as $key) {
                $value = $settings->$key ?? "";
                if ($key == 'banner_1' || $key == 'banner_2' || $key == 'banner_3'){

                    $obj[$key] = url(str_replace('public', 'storage', $value));
                }else{
                    $obj[$key] = $value;
                }
            } 

            if (!empty($categorySlug)) {
                $category = ProductCategory::where('slug', $categorySlug)->first();
                if ($category) {
                    $products = Affiliate::with('country')->whereHas('productCategories', function ($query) use ($category) {
                        $query->where('product_category_id', $category->id);
                    })
                        ->select('id', 'name', 'image', 'description', 'price', 'selling_price','country_id')
                        ->orderBy('id', 'desc')
                        ->paginate(24);
                } else {
                    return $this->returnResponse(404, 'failure', [], 'Category not found');
                }
            } else {
                $products = Affiliate::with('country')->select('id', 'name', 'image', 'description', 'price', 'selling_price', 'country_id')
                    ->paginate(24);
            }

            $productCategories  = ProductCategory::select('id', 'name', 'arabic_name','slug', 'created_at', 'updated_at')->get();

            $finalArray = [
                'settings' => $obj,
                'products' => $products,
                'category' => $category,
                'product_categories' => $productCategories,
            ];

            return $this->returnResponse(200, 'success', $finalArray, 'success');
        } catch (\Exception $e) {
            \Log::error('Error in getAffiliatePage:', ['error' => $e->getMessage()]);
            return $this->returnResponse(500, 'failure', [], 'An error occurred while processing your request');
        }
    }

    public function getTagPage()
    {
        $tag = request()->tag;

        if (empty($tag)) {
            return $this->missingParameter();
        }
        $tagInfo = Tag::select('id', 'tag_name', 'image')
            ->where('tag_name', $tag)
            ->first();

        

        $news = News::with(['category'])
            ->select(
                "id",
                'name',
                'slug',
                'content',
                'image',
                'category_id',
                'date',
                'views',
                'shares',
                'urgent',
                'video',
                'source_id',
                'source_link',
                'created_at'
            )

            ->where(function ($query) use ($tag) {
                 $query->orWhere('name', 'like', "%{$tag}%")
                            ->orWhere('excerpt', 'like', "%{$tag}%")
                            ->orWhere('content', 'like', "%{$tag}%");
            });

            if(!empty($tagInfo)) {
                $news->orWhereHas('tags', function ($q) use ($tagInfo) {
                    $q->where('tags.id', $tagInfo->id);
                });
            }

            $news->orderBy('id', 'desc');
          


        $news = $news->distinct('slug')->take(10)->paginate(10);
        $fullAds = $this->getFullAdsForLocation('Topic');



        $finalArray = [
            'news' => $news,
            'tag' => $tagInfo,
            'full_ads' => $fullAds,
        ];

        if (!empty($news)) {
            return $this->returnResponse(200, 'success', $finalArray, 'found');
        } else {
            return $this->returnResponse(403, 'failure', $finalArray, 'not found');
        }
    }

        public function getKeywordPage()
    {
        $keyword = request()->keyword;

        if (empty($keyword)) {
            return $this->missingParameter();
        }
        $keywordInfo = Keyword::select('id', 'keyword_name', 'description', 'image')
            ->where('keyword_name', $keyword)
            ->first();

        

        $news = News::with(['category'])
            ->select(
                "id",
                'name',
                'slug',
                'content',
                'image',
                'category_id',
                'date',
                'views',
                'shares',
                'urgent',
                'video',
                'source_id',
                'source_link',
                'created_at'
            )

            ->where(function ($query) use ($keyword) {
                 $query->orWhere('name', 'like', "%{$keyword}%")
                            ->orWhere('excerpt', 'like', "%{$keyword}%")
                            ->orWhere('content', 'like', "%{$keyword}%");
            });

            if(!empty($keywordInfo)) {
                $news->orWhereHas('keywords', function ($q) use ($keywordInfo) {
                    $q->where('keywords.id', $keywordInfo->id);
                });
            }

            $news->orderBy('id', 'desc');
          


        $news = $news->distinct('slug')->take(10)->paginate(10);
        $fullAds = $this->getFullAdsForLocation('Topic');



        $finalArray = [
            'news' => $news,
            'keyword' => $keywordInfo,
            'full_ads' => $fullAds,
        ];

        if (!empty($news)) {
            return $this->returnResponse(200, 'success', $finalArray, 'found');
        } else {
            return $this->returnResponse(403, 'failure', $finalArray, 'not found');
        }
    }

    public function addUserCategory()
    {
        try {
            $userId = request()->input('user_id');
            $categoryId = request()->input('category_id');

            if (empty($userId) || !is_numeric($userId)) {
                return response()->json([
                    'message' => 'Invalid or missing user_id.',
                ], 400);
            }

            if (empty($categoryId) || !is_numeric($categoryId)) {
                return response()->json([
                    'message' => 'Invalid or missing category_id.',
                ], 400);
            }

            $user = Advertiser::find($userId);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $category = Category::find($categoryId);
            if (!$category) {
                return response()->json([
                    'message' => 'Invalid category ID.',
                ], 400);
            }

            if ($user->categories()->where('categories.id', $categoryId)->exists()) {
                $user->categories()->detach($categoryId);
                return response()->json([
                    'message' => 'Category removed from user.',
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                ], 200);
            } else {
                $user->categories()->attach($categoryId);
                return response()->json([
                    'message' => 'Category successfully assigned to the user.',
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function addUserSources()
    {
        try {
            $userId = request()->input('user_id');
            $source = request()->input('source');

            if (empty($userId) || !is_numeric($userId)) {
                return response()->json([
                    'message' => 'Invalid or missing user_id.',
                ], 400);
            }

            if (is_numeric($source)) {
                $sourceId = $source;
            } else {
                $sourceRecord = Source::where('arabic_name', $source)->first();
                if (!$sourceRecord) {
                    return response()->json([
                        'message' => 'Invalid source name.',
                    ], 400);
                }
                $sourceId = $sourceRecord->id;
            }

            // dd($sourceId);

            if (empty($sourceId) || !is_numeric($sourceId)) {
                return response()->json([
                    'message' => 'Invalid or missing source_id.',
                ], 400);
            }

            $user = Advertiser::find($userId);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $source = Source::find($sourceId);
            if (!$source) {
                return response()->json([
                    'message' => 'Invalid Source ID.',
                ], 400);
            }

            if ($user->sources()->where('sources.id', $sourceId)->exists()) {
                $user->sources()->detach($sourceId);
                $source->save();
                return response()->json([
                    'message' => 'Source removed from user.',
                    'user_id' => $userId,
                    'source_id' => $sourceId,
                ], 200);
            } else {
                $user->sources()->attach($sourceId);
                $source->save();
                return response()->json([
                    'message' => 'Source successfully assigned to the user.',
                    'user_id' => $userId,
                    'source_id' => $sourceId,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleUserTopic()
    {
        try {
            $userId = request()->input('user_id');
            $keywordId = request()->input('keyword_id');

            if (empty($userId) || !is_numeric($userId)) {
                return response()->json([
                    'message' => 'Invalid or missing user_id.',
                ], 400);
            }

            if (empty($keywordId) || !is_numeric($keywordId)) {
                return response()->json([
                    'message' => 'Invalid or missing keyword_id.',
                ], 400);
            }

            $user = Advertiser::find($userId);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $keyword = Keyword::find($keywordId);
            if (!$keyword) {
                return response()->json([
                    'message' => 'Invalid keyword ID.',
                ], 400);
            }

            if ($user->keywords()->where('keywords.id', $keywordId)->exists()) {
                $user->keywords()->detach($keywordId);
                return response()->json([
                    'message' => 'Remove Successfully.'
                ], 400);
            }

            $user->keywords()->attach($keywordId);

            return response()->json([
                'message' => 'News Keyword Subscribed Successfully.',
                'source_id' => $keywordId,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSettingsObject()
    {
        try {
            $user = auth('api')->check() ? auth('api')->user() : null;

            $mainCategories = Category::select('id', 'name', 'arabic_name', 'icon_class')->get();
            $tags = Keyword::select('id', 'keyword_name')->orderBy('id', 'desc')->get();
            $userCategories = null;
            $userSources = null;
            $userId = null;

            if ($user) {
                $userId = $user->id;
                $userCategories = $user->categories()->select('categories.id', 'categories.name')->pluck('id');
                $userSources = $user->sources()->select('sources.id', 'sources.name')->pluck('id');
                $userTopics = $user->keywords()->select('keywords.id', 'keywords.keyword_name')->get();
            }

            $country_slug = request('country');

            $category_slug = request()->category_slug;

            $query = Source::query();

            if ($country_slug) {
                $country = Country::where('country_code', $country_slug)->first();
                if (!$country) {
                    return response()->json(['message' => 'Country not found'], 404);
                }

            $query->whereHas('countries', function ($q) use ($country) {
                $q->where('country_id', $country->id);
            });
            }

            if ($category_slug) {
                $category = Category::where('name', $category_slug)->first();
                if ($category) {
                    $sourceCategoryId = SourceFeed::where('category_id', $category->id)->pluck('source_id')->toArray();
                    $query->whereIn('id', $sourceCategoryId);
                }
            }

            $user_detail = ['first_name' => $user->adv_first_name, 'last_name' => $user->adv_last_name, 'email' => $user->adv_email];

            $sources = $query->with(['countries', 'categories'])->select('sources.id','sources.name','sources.arabic_name', 'sources.logo')->get();

            return response()->json([
                'message' => 'Settings retrieved successfully.',
                'user_id' => $userId,
                'user_details' => $user_detail,
                'main_categories' => $mainCategories,
                'tags' => $tags,
                'filtered_sources' => $sources,
                'user_categories' => $userCategories,
                'user_sources' => $userSources,
                'user_topics' => $userTopics,
                'email_notifications_enabled' => $user->email_notifications_enabled ? true : false,
                'push_notifications_enabled' => $user->push_notifications_enabled ? true : false,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function increaseVideoLikes()
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
        $likes = $newsItem->news_likes;
        $likes++;
        $newsItem->news_likes = $likes;
        $newsItem->save();
        $message = ($this->app_lang == "ar") ? " تمت الاضافة بنجاح" : "Inserted successfully";
        return $this->returnResponse(200, 'success', 1, $message);
    }

    public function getSubscribedSources()
    {
        try {
            if (!Auth::guard('api')->check()) {
                return response()->json([
                    'message'      => 'User is not authenticated.',
                    'user_sources' => null,
                ], 401);
            }

            $advertiser = Advertiser::find(Auth::guard('api')->id());
            $user = null;
            if (!empty($advertiser)) {
                $user = new AdvertiserResource($advertiser);
            }

            $user_sources = [];

            if ($user) {
                $sources = $user->sources()->select('sources.id', 'sources.arabic_name')->get();

                foreach ($sources as $source) {
                    $user_sources[] = [
                        'id'   => $source->id,
                        'name' => $source->arabic_name
                    ];
                }
            }

            return response()->json([
                'message'      => 'subscribed sources retrieved successfully.',
                'user_sources' => $user_sources,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message'      => 'An error occurred: ' . $e->getMessage(),
                'user_sources' => null,
            ], 500);
        }
    }

    public function changeUserDetail(Request $request)
    {
        try {
            $advertiser = Advertiser::find(Auth::guard('api')->id());

            if (!$advertiser) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $data = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:advertisers,adv_email,' . $advertiser->id,
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $advertiser->adv_first_name = $data['firstname'];
            $advertiser->adv_last_name = $data['lastname'];
            $advertiser->adv_email = $data['email'];

            if (!empty($data['password'])) {
                $advertiser->adv_password = Hash::make($data['password']);
            }

            $advertiser->save();

            return response()->json(['message' => 'User details updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
