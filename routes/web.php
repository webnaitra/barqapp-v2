<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewCounterController;

Route::get('/', function () {
    return view('welcome');
});

Route::any('/videos/{id}/view', [ViewCounterController::class, 'video'])->name('video_share_view');
Route::any('/affiliate/{id}/view', [ViewCounterController::class, 'affiliate'])->name('affiliate_share_view');
Route::any('/ads/{id}/view', [ViewCounterController::class, 'ads'])->name('ads_share_view');