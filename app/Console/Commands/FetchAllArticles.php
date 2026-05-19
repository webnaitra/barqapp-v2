<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Services\FetchArticlesService;

use App\Models\SourceFeed;
use App\Models\Source;
use App\Models\News;
use App\Models\Tag;
use App\Models\NewsTags;


class FetchAllArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:fetch-all-articles {showOutput=0} {categoryId=null} {sourceId=null} {sourcefeedId=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is jawalatt cron to get rss feed and save in database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(FetchArticlesService $service)
    {
        $showOutput = $this->argument('showOutput');
        $categoryId = $this->argument('categoryId');
        $sourceId = $this->argument('sourceId');
        $sourcefeedId = $this->argument('sourcefeedId');

        if ($categoryId === 'null') $categoryId = null;
        if ($sourceId === 'null') $sourceId = null;
        if ($sourcefeedId === 'null') $sourcefeedId = null;

        $query = SourceFeed::with(['source', 'category'])->whereHas('source', function($q) {
            $q->select('name','source_url','category_id','source_id');
        });

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($sourceId) {
            $query->where('source_id', $sourceId);
        }
        if ($sourcefeedId) {
            $query->where('id', $sourcefeedId);
        }

        $allFeeds = $query->get();
        $feeds = collect();
        $now = \Carbon\Carbon::now();

        foreach ($allFeeds as $feed) {
            $frequency = $feed->source->fetch_frequency ?? $feed->category->fetch_frequency ?? 30; // Default 30 mins
            
            if (is_null($feed->last_fetched_at)) {
                $feeds->push($feed);
                continue;
            }

            $lastFetched = \Carbon\Carbon::parse($feed->last_fetched_at);
            if ($now->diffInMinutes($lastFetched) >= $frequency) {
                $feeds->push($feed);
            }
        }

        $totalFeeds = $feeds->count();

        $this->info("Total feeds to process: $totalFeeds");

        if ($totalFeeds === 0) {
            return 0;
        }

        if ($showOutput) {
            $this->info("Running synchronously (showOutput = 1)...");
            foreach ($feeds as $feed) {
                $this->info("Processing feed: " . $feed->source_url);
                $service->processSingleFeed($feed, null, false);
            }
            $this->info("Finished synchronous processing.");
        } else {
            $this->info("Dispatching background jobs...");
            $jobs = [];
            foreach ($feeds as $feed) {
                $jobs[] = new \App\Jobs\ProcessFeedJob($feed, false);
            }

            $batch = \Illuminate\Support\Facades\Bus::batch($jobs)
                ->name('Fetch Articles Command (Cron)')
                ->onQueue('fetch-articles')
                ->dispatch();

            $this->info("Dispatched batch ID: " . $batch->id);
            $this->info("Make sure your queue worker is running (php artisan queue:work).");
        }

        return 0;
    }
}
