<?php

namespace App\Observers;

use App\Models\News;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Jobs\SingleCron;
use Carbon\Carbon;

class NewsItemObserver
{
    /**
     * Handle the News "created" event.
     *
     * @param  \App\Models\News  $news
     * @return void
     */
    public function created(News $news)
    {
        dispatch(new SingleCron($news->id))->delay(Carbon::now()->addSeconds(30));
    }

    /**
     * Handle the News "updated" event.
     *
     * @param  \App\Models\News  $newsItem
     * @return void
     */
    public function updated(News $newsItem)
    {
        //
    }

    /**
     * Handle the News "deleted" event.
     *
     * @param  \App\Models\News  $newsItem
     * @return void
     */
    public function deleted(News $newsItem)
    {
        if (str_contains($newsItem->image, 'https://phplaravel-920759-3202760.cloudwaysapps.com/storage/') || str_contains($newsItem->image, 'https://dashboard.barqapp.net/storage/')) {
            $str = $newsItem->image;
            $str = str_replace("https://phplaravel-920759-3202760.cloudwaysapps.com/storage/","", $str);
            $str = str_replace("https://dashboard.barqapp.net/storage/","", $str);
            Storage::disk('public')->delete($str);
        }
    }

    /**
     * Handle the News "restored" event.
     *
     * @param  \App\Models\News  $newsItem
     * @return void
     */
    public function restored(News $newsItem)
    {
        //
    }

    /**
     * Handle the News "force deleted" event.
     *
     * @param  \App\Models\News  $newsItem
     * @return void
     */
    public function forceDeleted(News $newsItem)
    {
        //
    }
}
