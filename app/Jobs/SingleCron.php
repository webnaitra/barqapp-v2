<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Models\News;

class SingleCron implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $news;

    // override the queue tries
    // configuration for this job
    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($news)
    {
        $this->news = $news;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
            try {
                $exitCode = Artisan::call('cron:fetch-articles', [
                    'news' => $this->news
                ]);
            } catch (\Throwable $exception) {
                if ($this->attempts() > 2) {
                    // hard fail after 3 attempts
                    throw $exception;
                }

                Log::info($exception);

                // requeue this job to be executes
                // in 3 minutes (180 seconds) from now
                $this->release(180);
                return;
            }        
    }
}
