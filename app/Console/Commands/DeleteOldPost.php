<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Carbon\Carbon;

class DeleteOldPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:oldpost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $news = News::where('created_at', '<', Carbon::now()->subDays(env('POST_EXPIRATION_DURATION_DAYS')))->take(env('POST_EXPIRATION_POST_LENGTH'))->get();

        foreach($news as $item){
            News::find($item->id)->delete();
        }
        return 0;
    }
}
