<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebApiController;
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Middleware\LogIncomingRequests;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::any('/register', 'Api\AuthController@register');
Route::any('/loginAuth', 'Api\AuthController@login');
// Route::any('/test', 'Api\ApiController@test')->middleware('auth:api');
//Route::any('/signup', 'Api\ApiController@signup')->middleware('auth:api');
Route::any('/signup', 'Api\ApiController@signup');
//Route::any('/activateAccount', 'Api\ApiController@activateAccount')->middleware('auth:api');
//Route::any('/activateAccount', 'Api\ApiController@activateAccount');
//Route::any('/login', 'Api\ApiController@login')->middleware('auth:api');
Route::any('/login', 'Api\ApiController@login');
//Route::any('/resendActivationCode', 'Api\ApiController@resendActivationCode')->middleware('auth:api');
Route::any('/forgotPassword', 'Api\ApiController@forgotPassword');
Route::any('/resetPassword', 'Api\ApiController@resetPassword');

/*********** Routes for Web App ******************/
Route::get('/web/getServer', [WebApiController::class, 'getServer'])->name('api.web.getServer');
Route::get('/web/getHomePage', [WebApiController::class, 'getHomePage'])->name('api.web.getHomePage');
Route::get('/web/getHomePageDev', [WebApiController::class, 'getHomePageDev'])->name('api.web.getHomePageDev');
Route::post('/web/changeUserDetail', [WebApiController::class, 'changeUserDetail'])->name('api.web.changeUserDetail');
Route::get('/web/getSearchPage', [WebApiController::class, 'getSearchPage'])->name('api.web.getSearchPage');
Route::get('/web/getCategoryPage', [WebApiController::class, 'getCategoryPage'])->name('api.web.getCategoryPage');
Route::get('/web/getKeywordPage', [WebApiController::class, 'getKeywordPage'])->name('api.web.getKeywordPage');
Route::get('/web/getNewsSourcePage', [WebApiController::class, 'getNewsSourcePage'])->name('api.web.getNewsSourcePage');
Route::get('/web/getSubCategoryPage', [WebApiController::class, 'getSubCategoryPage'])->name('api.web.getSubCategoryPage');
Route::get('/web/getSingleNewsPage', [WebApiController::class, 'getSingleNewsPage'])->name('api.web.getSingleNewsPage');
Route::get('/web/getCoverageNews', [WebApiController::class, 'getCoverageNews'])->name('api.web.getCoverageNews');
Route::get('/web/getMainCategories', [WebApiController::class, 'getMainCategories'])->name('api.web.getMainCategories');
Route::get('/web/getMainSources', [WebApiController::class, 'getMainSources'])->name('api.web.getMainSources');
Route::get('/web/getPage', [WebApiController::class, 'getPage'])->name('api.web.getPage');
Route::post('/web/subscribeToNewsletter', [WebApiController::class, 'subscribeToNewsletter'])->name('api.web.subscribeToNewsLetter');
Route::post('/web/login', [WebApiController::class, 'login'])->name('api.web.login');
Route::post('/web/signup', [WebApiController::class, 'signup'])->name('api.web.signup');
Route::post('/web/forgotPassword', [WebApiController::class, 'forgotPassword'])->name('api.web.forgotPassword');
Route::post('/web/verifyCode', [WebApiController::class, 'verifyCode'])->name('api.web.forgotPassword');
Route::post('/web/resetPassword', [WebApiController::class, 'resetPassword'])->name('api.web.resetPassword');
Route::post('/web/addUserFavorite', [WebApiController::class, 'addUserFavorite'])->name('api.web.addUserFavorite');
Route::post('/web/deleteUserFavorite', [WebApiController::class, 'deleteUserFavorite'])->name('api.web.deleteUserFavorite');
Route::get('/web/getLiveStreams', [WebApiController::class, 'getLiveStreams'])->name('api.web.getLiveStreams');
Route::get('/web/getGoldRate', [WebApiController::class, 'getGoldRate'])->name('api.web.getGoldRate');
Route::get('/web/getExchangeRates', [WebApiController::class, 'getExchangeRates'])->name('api.web.getExchangeRates');
Route::get('/web/getVideoPage', [WebApiController::class, 'getVideoPage'])->name('api.web.getVideoPage');
Route::get('/web/getUserFavorites', [WebApiController::class, 'getUserFavorites'])->name('api.web.getUserFavorites');
Route::get('/web/getAffiliatePage', [WebApiController::class, 'getAffiliatePage'])->name('api.web.getAffiliatePage');
Route::get('/web/getKeywordSearchPage', [WebApiController::class, 'getKeywordPage'])->name('api.web.getKeywordSearchPage');
Route::get('/web/getAllCountries', [WebApiController::class, 'getAllCountries'])->name('api.web.getAllCountries');
Route::get('/web/getSettingsObject', [WebApiController::class, 'getSettingsObject'])->name('api.web.getSettingsObject');
Route::post('/web/addUserCategory', [WebApiController::class, 'addUserCategory'])->name('api.web.addUserCategory');
Route::post('/web/increaseVideoLikes', [WebApiController::class, 'increaseVideoLikes'])->name('api.web.increaseVideoLikes');
Route::post('/web/addUserSources', [WebApiController::class, 'addUserSources'])->name('api.web.addUserSources');
Route::get('/web/getSubscribedSources', [WebApiController::class, 'getSubscribedSources'])->name('api.web.getSubscribedSources');
Route::post('/web/addUserTopic', [WebApiController::class, 'toggleUserTopic'])->name('api.web.addUserTopic');
Route::post('/web/detachUserTopic', [WebApiController::class, 'toggleUserTopic'])->name('api.web.detachUserTopic');
Route::post('/web/updatePushPreference', [WebApiController::class, 'updatePushPreference'])->name('api.web.updatePushPreference');
Route::post('/web/updateEmailPreference', [WebApiController::class, 'updateEmailPreference'])->name('api.web.updateEmailPreference');
Route::any('/increaseShares', 'Api\ApiController@increaseShares');
Route::middleware('auth:api')->group(function () {
Route::get('web/notification-preferences', [NotificationController::class, 'getPreferences']);
});


// Route::get('/getUserFavorites', 'Api\ApiController@getUserFavorites');

/*********** End Routes for Web App ************/

/*********** Routes for Mobile App  ***********/

Route::prefix('mobile/v1')->middleware(['log_api_requests'])->group(function () {
    Route::post('/register', [MobileApiController::class, 'mobileApiMakeNewAuthor']);
    Route::post('/login', [MobileApiController::class, 'mobileloginapi']);
    Route::post('/forgotPassword', [MobileApiController::class, 'forgotPasswordMob']);
    Route::post('/resetPassword', [MobileApiController::class, 'resetPasswordMob']);
    Route::post('/verify_email', [MobileApiController::class, 'verifyEmail']);
    Route::get('/retrieve_password', [MobileApiController::class, 'retrivePassword']);
    Route::post('/update_profile', [MobileApiController::class, 'updateProfile']);
    Route::post('/home_post', [MobileApiController::class, 'get_home_post']);
    Route::post('/category_post', [MobileApiController::class, 'get_category_post']);
    Route::post('/source_post', [MobileApiController::class, 'get_source_post']);
    Route::post('/single_post', [MobileApiController::class, 'single_post']);
    Route::get('/all_category_list', [MobileApiController::class, 'all_category_list']);
    Route::get('/all_source_list', [MobileApiController::class, 'all_source_list'])->middleware(LogIncomingRequests::class);
    Route::post('/search_news', [MobileApiController::class, 'search_news']);
    Route::post('/add_bookmark', [MobileApiController::class, 'add_bookmark']);
    Route::post('/get_bookmark', [MobileApiController::class, 'getbookmarks']);
    Route::post('/add_suggest', [MobileApiController::class, 'add_suggest']);
    Route::post('/all_ads', [MobileApiController::class, 'header_add']);
    Route::get('/barqapp_list', [MobileApiController::class, 'barqapp_list']);
    Route::post('/adds_screen', [MobileApiController::class, 'adds_screen']);
    Route::post('/check_source', [MobileApiController::class, 'check_source']);
    Route::post('/get_user_source', [MobileApiController::class, 'get_user_source']);
    Route::get('/get_user_selected', [MobileApiController::class, 'get_user_selected']);
    Route::get('/get_video_details', [MobileApiController::class, 'get_video_details']);
    Route::post('/get_country_post', [MobileApiController::class, 'get_country_post']);
    Route::post('/social_login', [MobileApiController::class, 'social_login']);
    Route::get('/all_country_list', [MobileApiController::class, 'all_country_list']);
    Route::get('/category_menu', [MobileApiController::class, 'category_menu']);
    Route::post('/fav_menu_cate', [MobileApiController::class, 'fav_menu_cate']);
    Route::post('/get_fav_menu_cate', [MobileApiController::class, 'get_fav_menu_cate']);
    Route::get('/home_simple', [MobileApiController::class, 'home_simple']);
    // Route::middleware('auth:api')->get('/get_logged_user', [MobileApiController::class, 'getLoggedUser']);
    Route::get('/get_logged_user', [MobileApiController::class, 'getLoggedUser']);
    Route::post('/search_by_cat_news', [MobileApiController::class, 'search_by_cat_news']);
    Route::get('/breaking_news', [MobileApiController::class, 'breaking_news']);
    Route::get('/sports', [MobileApiController::class, 'sports']);
    Route::get('/economy', [MobileApiController::class, 'economy']);
    Route::get('/health', [MobileApiController::class, 'health']);
    Route::get('/tourism', [MobileApiController::class, 'tourism']);
    Route::get('/about', [MobileApiController::class, 'about']);
    Route::post('/country_wise', [MobileApiController::class, 'country_wise']);
    Route::get('/child_category', [MobileApiController::class, 'child_category']);
    Route::post('/delete_user', [MobileApiController::class, 'delete_user']);

    // Other routes...
});

/*********** End Routes for Mobile App ********/

Route::middleware('auth:api')->group(function () {
    Route::any('/homepage', 'Api\ApiController@homepage');
    Route::any('/save_contactus', 'Api\ApiController@saveContactUs');
    Route::any('/ads_areas', 'Api\ApiController@ads_areas');

    Route::any('/getMainCategories', 'Api\ApiController@getMainCategories');
    Route::any('/HomePageCategory', 'Api\ApiController@HomePageCategory');

    Route::any('/getFooterMenu', 'Api\ApiController@getFooterMenu');
    Route::any('/getAdsense', 'Api\ApiController@getAdsense');
    Route::any('/getNews', 'Api\ApiController@getNews');
    Route::any('/SearchNews', 'Api\ApiController@SearchNews');
    Route::any('/increaseViews', 'Api\ApiController@increaseViews');
    Route::any('/subscribeToNewsletter', 'Api\ApiController@subscribeToNewsletter');
    Route::any('/subscribeToWhatsapp', 'Api\ApiController@subscribeToWhatsapp');
    Route::any('/getMobileNews', 'Api\ApiController@getMobileNews');
    Route::any('/updateNotificationSettings', 'Api\ApiController@updateNotificationSettings');
    Route::any('/getNotificationSettings', 'Api\ApiController@getNotificationSettings');
    Route::any('/getTerms', 'Api\ApiController@getTerms');
    Route::any('/getPage', 'Api\ApiController@getPage');
    Route::any('/getUserRates', 'Api\ApiController@getUserRates');
    Route::any('/addInterest', 'Api\ApiController@addInterest');
    Route::any('/getInterests', 'Api\ApiController@getInterests');
    Route::any('/aboutApp', 'Api\ApiController@aboutApp');
    Route::any('/appIntroduction', 'Api\ApiController@appIntroduction');
    Route::any('/appPrivacy', 'Api\ApiController@appPrivacy');
    Route::any('/deleteInterest', 'Api\ApiController@deleteInterest');
    Route::any('/getMenu', 'Api\ApiController@getMenu');
    Route::any('/appTerms', 'Api\ApiController@appPrivacy');
    Route::any('/getUserData', 'Api\ApiController@getUserData');
    Route::any('/updateProfile', 'Api\ApiController@updateProfile');
    Route::any('/getSettings', 'Api\ApiController@getSettings');
    Route::any('/addUserFavorite', 'Api\ApiController@addUserFavorite');
    Route::any('/deleteUserFavorite', 'Api\ApiController@deleteUserFavorite');
    Route::any('/getPhonesRates', 'Api\ApiController@getPhonesRates');
    Route::any('/changeLang', 'Api\ApiController@changeLang');

    Route::any('/getUserNotificationsCount', 'Api\ApiController@getUserNotificationsCount');

    Route::any('/getSocialLinks', 'Api\ApiController@getSocialLinks');
});
