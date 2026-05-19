<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\SourceFeed;
use App\Services\FetchArticlesService;

class ProcessFeedJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $feed;
    public $dryRun;

    /**
     * Create a new job instance.
     */
    public function __construct(SourceFeed $feed, $dryRun = false)
    {
        $this->feed = $feed;
        $this->dryRun = $dryRun;
    }

    /**
     * Execute the job.
     */
    public function handle(FetchArticlesService $service): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $batchId = $this->batch() ? $this->batch()->id : null;
        
        // Pass the batchId to the service so it can log to article_fetch_logs
        $service->processSingleFeed($this->feed, $batchId, $this->dryRun);
    }
}
