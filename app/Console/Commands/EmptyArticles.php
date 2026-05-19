<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Source;
use App\Models\News;
use App\Models\Category;
use App\Settings\GeneralSettings;

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

        $isManualTrigger = ($categoryId !== 'null' && $categoryId !== null) ||
                           ($sourceId !== 'null' && $sourceId !== null) ||
                           ($sourcefeedId !== 'null' && $sourcefeedId !== null) ||
                           ($olderThanDays !== 'null' && $olderThanDays !== null);

        if ($isManualTrigger) {
            // Manual trigger logic
            News::when($categoryId !== 'null' && $categoryId !== null, function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($sourceId !== 'null' && $sourceId !== null, function ($query) use ($sourceId) {
                return $query->where('source_id', $sourceId);
            })
            ->when($sourcefeedId !== 'null' && $sourcefeedId !== null, function ($query) use ($sourcefeedId) {
                return $query->where('sourcefeed_id', $sourcefeedId);
            })
            ->when($olderThanDays !== 'null' && $olderThanDays !== null, function ($query) use ($olderThanDays) {
                return $query->where('created_at', '<=', now()->subDays($olderThanDays));
            })
            ->whereDoesntHave('category', function ($q) {
                $q->where('freeze', 1);
            })
            ->whereDoesntHave('sources', function ($q) {
                $q->where('freeze', 1);
            })
            ->where('urgent', 0)
            ->delete();

            $this->info("Manually emptied articles based on provided filters.");
            return 0;
        }

        // Automated execution (no arguments provided)
        $globalDuration = app(GeneralSettings::class)->auto_expire_duration ?? 3;

        $combinations = News::select('source_id', 'category_id')->distinct()->get();
        
        $sources = Source::pluck('freeze', 'id')->toArray();
        $categories = Category::pluck('freeze', 'id')->toArray();
        $sourceDurations = Source::pluck('auto_expire_duration', 'id')->toArray();
        $categoryDurations = Category::pluck('auto_expire_duration', 'id')->toArray();

        $deletedCount = 0;

        foreach ($combinations as $combo) {
            $sId = $combo->source_id;
            $cId = $combo->category_id;

            if (($sources[$sId] ?? 0) == 1 || ($categories[$cId] ?? 0) == 1) {
                continue; // Skip frozen
            }

            $duration = $sourceDurations[$sId] ?? $categoryDurations[$cId] ?? $globalDuration;

            $deleted = News::where('source_id', $sId)
                ->where('category_id', $cId)
                ->where('urgent', 0)
                ->where('created_at', '<=', now()->subDays($duration))
                ->delete();

            $deletedCount += $deleted;
        }

        $this->info("Automated cleanup finished. Deleted {$deletedCount} old articles.");
        return 0;
    }
}
