<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Source;
use App\Models\News;

class EmptyArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:empty-articles {categoryId=null} {sourceId=null} {sourcefeedId=null} {olderThanDays=null}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empty articles table based on filters';

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
    public function handle()
    {

        $categoryId = $this->argument('categoryId');
        $sourceId = $this->argument('sourceId');
        $sourcefeedId = $this->argument('sourcefeedId');
        $olderThanDays = $this->argument('olderThanDays');

        News::when($categoryId != 'null', function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        })
        ->when($sourceId != 'null', function ($query) use ($sourceId) {
            return $query->where('source_id', $sourceId);
        })
        ->when($sourcefeedId != 'null', function ($query) use ($sourcefeedId) {
            return $query->where('sourcefeed_id', $sourcefeedId);
        })
        ->when($olderThanDays != 'null', function ($query) use ($olderThanDays) {
            return $query->where('created_at', '<=', now()->subDays($olderThanDays));
        })
        // only delete news where none of the related models are frozen
        ->whereDoesntHave('category', function ($q) {
            $q->where('freeze', 1);
        })
        ->whereDoesntHave('sources', function ($q) {
            $q->where('freeze', 1);
        })
        ->whereDoesntHave('sourcefeed', function ($q) {
            $q->where('freeze', 1);
        })
        ->where('urgent', 0)
        ->delete();

        return 0;
    }
}
