<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::any('/videos/{id}/view', 'ViewCounterController@video')->name('video_share_view');
Route::any('/affiliate/{id}/view', 'ViewCounterController@affiliate')->name('affiliate_share_view');
Route::any('/ads/{id}/view', 'ViewCounterController@ads')->name('ads_share_view');
