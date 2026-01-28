<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ViewCounterController;

Route::get('/', function () {
    return view('welcome');
});

Route::any('/videos/{id}/view', [ViewCounterController::class, 'video'])->name('video_share_view');
Route::any('/affiliate/{id}/view', [ViewCounterController::class, 'affiliate'])->name('affiliate_share_view');
Route::any('/ads/{id}/view', [ViewCounterController::class, 'ads'])->name('ads_share_view');


Route::get('/run/cron/job', function () {
    $exitCode = Artisan::call('cron:fetch-all-articles', array('showOutput' => 1));
})->name('cron.job');

Route::get('/run/cron/job/category/{categoryId}', function ($categoryId) {
    $exitCode = Artisan::call('cron:fetch-all-articles', array('showOutput' => 1, 'categoryId' => $categoryId));
})->name('category.cron');

Route::get('/run/cron/job/source/{sourceId}', function ($sourceId) {
    $exitCode = Artisan::call('cron:fetch-all-articles', array('showOutput' => 1, 'sourceId' => $sourceId));
})->name('source.cron');

Route::get('/empty/category/{categoryId}', function ($categoryId) {
    $exitCode = Artisan::call('cron:empty-articles', array('categoryId' => $categoryId, 'olderThanDays' => 3));
    echo "Articles Emptied (Older than 3 days)";
})->name('category.empty');

Route::get('/empty/source/{sourceId}', function ($sourceId) {
    $exitCode = Artisan::call('cron:empty-articles', array('sourceId' => $sourceId, 'olderThanDays' => 3));
    echo "Articles Emptied (Older than 3 days)";
})->name('source.empty');