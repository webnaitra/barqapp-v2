<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\News;
use App\Models\Dbarchieve;
use App\Models\NewsArchieve;
use DB;
use Carbon\Carbon;


class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cretae New Database copy of Current Database Every Month';

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

        $start = new \DateTime('first day of this month');
        $start->format('y/m/d');
        $today = date('y/m/d');
//        if($start == $today){

            try {
                $news_id = [];
                $last_100_news = \App\Models\News::orderBy('id', 'desc')->take(100)->pluck('id')->ToArray();
                $news = \App\Models\News::whereNotIn('id',$last_100_news)->get();

                $date = date('F_Y', strtotime("first day of previous month"));
                $db_achive = [];
                if($start == $today){
                    $schemaName = 'barqapp_'.$date ."_1";

                }else{

                    $schemaName = 'barqapp_'.$date."_2";

                }
                $db_achive['db_name'] = $schemaName;
                foreach($news as $copynews){

                    array_push($news_id,$copynews->id);

                    NewsArchieve::create([
                        'id'=>$copynews->id,
                        'news_title'=>$copynews->news_title,
                        'db_name'=>$schemaName
                    ]);

                }

                Schema::create($schemaName, function (Blueprint $table) {
                    $table->id();
                    $table->string('title')->nullable();
                    $table->string('name')->nullable();
                    $table->longText('content')->nullable();
                    $table->string('date')->nullable();
                    $table->string('image')->nullable();
                    $table->Integer('category_id')->nullable();
                    $table->Integer('views')->nullable()->default(0);
                    $table->Integer('shares')->nullable()->default(0);
                    $table->Integer('urgent')->nullable()->default(0);
                    $table->string('video')->nullable();
                    $table->string('source')->nullable();
                    $table->string('source_link')->nullable();
                    $table->Integer('old_id')->nullable();
                    $table->Integer('user_id')->nullable()->default(1);
                    $table->string('excerpt')->nullable();
                    $table->Integer('old_link')->nullable();
                    $table->tinyInteger('run_cron')->nullable()->default(1);
                    $table->tinyInteger('rss_feed')->nullable()->default(1);
                    $table->string('rss_url')->nullable();
                    $table->string('site')->nullable();
                    $table->string('country')->nullable();

                    $table->timestamps();
                });
                DB::insert("INSERT INTO ".$schemaName." SELECT * FROM  news");

                \App\Models\News::whereIn('id',$news_id)->delete();
                \App\Models\Dbarchieve::create($db_achive);


            } catch (\Illuminate\Database\QueryException $e) {
                $this->info($e->getMessage());
            }

        }

//    }
}
