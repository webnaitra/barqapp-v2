<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client as GoutteClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\News;
use App\Models\Source;
use App\Models\Current_Tags;
use App\Models\NewsTags;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;


class FetchFullArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:fetch-articles {news}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch full articles for Jawlatt';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function check_words($text, $keywords) {
        foreach ($keywords as $word) {
            if (mb_strpos($text, $word) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Extract Keywords  from text and sort them on the basis of recurrence
     */
    public function extract_keywords($html, $keywords) {
        // Convert the HTML to plain text
        $text = strip_tags($html);
        
    
        // Convert the text to lowercase
        $text = mb_strtolower($text);
        //dd($text);
        // Initialize an array to hold the matches
        $matches = [];
        
        // Loop through the keywords
        foreach ($keywords as $keyword) {
            // Convert the keyword to lowercase
            $keywordLower = mb_strtolower($keyword);
            $keywordArr = explode(" ", $keywordLower);
    
            // Check if the keyword is in the text
            if ($this->check_words($text, $keywordArr) !== false) {
                // If the keyword is in the text, add it to the matches
    
                $matches[$keyword] = count($keywordArr) > 1 ?  1 : substr_count($text, $keywordLower);
            }
        }
        arsort($matches);
        // Return the matches
        return $matches;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        
        $newsId = $this->argument('news');
        
        $news = News::find($newsId);
        Log::info('Loop 1 '.$newsId);

        if($news){
            
            sleep(3);
            $start_time = microtime(true);
            Log::info('Loop 1 '.$newsId);
            $client = new GoutteClient();
            $the_site = extract_url($news->source_link);
            Log::info('Loop source '.$the_site);
            $data = $client->request('GET', $the_site);
            Log::info('Loop 2 '.$newsId);
            $source = Source::where('id', $news->source_id)->first();

            if($source){
                Log::info('Loop 2 '.$newsId);
                $content_classes = $source->content_classes;
                $filtered_classes = $source->filter_classes;
                $image_classes = $source->image_classes;
                $html = '';


                /*** Select the content classes from the articles ***/

                if(!empty($content_classes)){
                    $elements = $data->filter($content_classes)->each(function($node){
                        return $node->html();
                    });

                    $elements = implode(' ', $elements);
                    $data = new Crawler($elements);
                }

                if(!empty($filtered_classes)){
                    Log::info('Loop 3 '.$newsId);
                    $data->filter($filtered_classes)->each(function($crawler){
                        foreach ($crawler as $node) {
                            $node->parentNode->removeChild($node);
                        }
                    });
                }

  

                
 

                /*** Check for youtube Video ***/
                    Log::info('Loop 5 '.$newsId);
                    $youtube = $data->filterXPath("//iframe")->each(function($node){
                        $src = $node->attr('src');

                        if(!empty($src)){
                            $parse = parse_url($src);
                            $host = $parse["host"];
                            $host = str_ireplace("www.", "", $host);
                            if ($host == "youtube.com") {
                                return $src;
                            } 
                        }


                        return false;
                        
                    });

                    $youtube = array_filter($youtube);

                    if(isset($youtube[0]) && !empty($youtube[0])){
                        $news->video = $youtube[0];
                        $news->is_updated = 1;
                    }
                    Log::info('Loop 6 '.$newsId);

                /*** Check for the Og image ***/

                $year = date('Y');
                $month = date('m');
                $day = date('d');
                $arrContextOptions=array(
                    "ssl" => array(
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ),
                ); 

/*** Extract main image from the articles */
                    $img_source = null;

                    // First, try to get image from custom selector if provided
                    if(!empty($image_classes)){
                        $imageNode = $data->filter($image_classes)->first();
                        
                        if ($imageNode->count() > 0) {
                            $domNode = $imageNode->getNode(0);
                            
                            // Check if it's an img tag
                            if ($domNode->nodeName === 'img') {
                                $img_source = $imageNode->attr('src');
                            } 
                            // Check if it's a figure containing an img
                            elseif ($domNode->nodeName === 'figure') {
                                $figureImg = $imageNode->filter('img')->first();
                                if ($figureImg->count() > 0) {
                                    $img_source = $figureImg->attr('src');
                                }
                            }
                            // For other elements (like div), look for img inside
                            else {
                                $innerImg = $imageNode->filter('img')->first();
                                if ($innerImg->count() > 0) {
                                    $img_source = $innerImg->attr('src');
                                }
                            }
                            
                            // Remove the node after extracting the image
                            if ($domNode && $domNode->parentNode) {
                                $domNode->parentNode->removeChild($domNode);
                            }
                        }
                    }

                    // If no image from custom selector, try first figure in content
                    if (empty($img_source)) {
                        $firstFigure = $data->filter('figure')->first();
                        if ($firstFigure->count() > 0) {
                            $figureImg = $firstFigure->filter('img')->first();
                            if ($figureImg->count() > 0) {
                                $img_source = $figureImg->attr('src');
                                
                                // Remove the figure after extracting
                                $figureNode = $firstFigure->getNode(0);
                                if ($figureNode && $figureNode->parentNode) {
                                    $figureNode->parentNode->removeChild($figureNode);
                                }
                            }
                        }
                    }

                    // If still no image, get first img in the content
                    if (empty($img_source)) {
                        $firstImg = $data->filter('img')->first();
                        if ($firstImg->count() > 0) {
                            $img_source = $firstImg->attr('src');
                            
                            // Remove the img after extracting
                            $imgNode = $firstImg->getNode(0);
                            if ($imgNode && $imgNode->parentNode) {
                                $imgNode->parentNode->removeChild($imgNode);
                            }
                        }
                    }

                    // Fallback to og:image if nothing found
                    if (empty($img_source)) {
                        $img_source = $data->filterXPath("//meta[@property='og:image']")->count() 
                            ? $data->filterXPath("//meta[@property='og:image']")->attr('content') 
                            : null;
                    }
                if(!empty($img_source)){
                        $news->image = $img_source;
                        $news->is_updated = 1;
                    // if(str_contains($img_source, 'https://cdn4.premiumread.com')){
                    //     $img_source_query = parse_url($img_source, PHP_URL_QUERY);
                    //     parse_str($img_source_query, $img_source_params);
                    //     $img_source = $img_source_params['url'];
                    //     Log::info('Al-madinna Image '.$img_source.' id '.$news->id);
                    // }

                    // Log::info('Has Image '.$news->name);
                    // $img_source_path_info = pathinfo(parse_url($img_source)['path']);
                    // if(isset($img_source_path_info['extension']) && isset($img_source_path_info['filename'])){
                    //     $img_source_extension = $img_source_path_info['extension'];
                    //     $img_source_filename = Str::slug($img_source_path_info['filename'], '-');
    
                    //     $img_content = file_get_contents($img_source);
                    //     $now = Carbon::now();
                    //     $now->year;
                    //     $now->month;
                    //     $news_image_dir = '/public_html/storage/app/public/images/'.$now->year.'/'.$now->month.'/';
                    //     $news_image_path = '/public_html/storage/app/public/images/'.$now->year.'/'.$now->month.'/'.$newsId.'-'.$img_source_filename.'.'.$img_source_extension;
                    //     $news_image_path_db = env('DASHBOARD_IMAGE_URI').'/images/'.$now->year.'/'.$now->month.'/'.$newsId.'-'.$img_source_filename.'.'.$img_source_extension;
                    //     Storage::disk('sftp')->put($news_image_path, $img_content);
                    //     Storage::disk('sftp')->setVisibility($news_image_dir, 'public');
                    //     $news->image = $news_image_path_db;
                    //     $news->is_updated = 1;
                    //     Log::info('Image updated'.$newsId);
                    // }
                }

                /*** Removes the filtered classes from the content ***/
                    if(!empty($filtered_classes)){
                        $data->filter($filtered_classes)->each(function($crawler){
                            foreach ($crawler as $node) {
                                $node->parentNode->removeChild($node);
                            }
                        });
    
                    }

                /*** Remove images inside links ***/
                    $data->filter('a')->each(function($node){
                        $domNode = $node->getNode(0);
                        if ($domNode && $domNode->parentNode && $domNode->firstChild == 'img') {
                            $domNode->parentNode->removeChild($domNode);
                        }
                    });

                 $data->filter('img, figure, script, style, noscript')->each(function($node){
                        $domNode = $node->getNode(0);
                        if ($domNode && $domNode->parentNode) {
                            $domNode->parentNode->removeChild($domNode);
                        }
                    });


                    // Remove all inline styles
                    $data->filter('[style]')->each(function($node) {
                        $domNode = $node->getNode(0);
                        if ($domNode) {
                            $domNode->removeAttribute('style');
                        }
                    });

                    // Remove all classes
                    $data->filter('[class]')->each(function($node) {
                        $domNode = $node->getNode(0);
                        if ($domNode) {
                            $domNode->removeAttribute('class');
                        }
                    });

                if(!empty($html)){
                    Log::info('Loop 7 1'.$newsId);
                    //Log::info('News ID: '. $newsId . ', News Title: '.$news->news_title );
                    $news->content = $html;
                    $news->is_updated = 1;
                }


                $news->save();

                $keywords = Current_Tags::pluck('tag_name')->all();
                $tags_to_assign = $this->extract_keywords($html, $keywords);
                
                
                foreach($tags_to_assign as $tag_name=>$tag_id){
                    $tag = Current_Tags::where('tag_name', $tag_name)->first();
                    if(!NewsTags::where('news_id', $news->id)->where('tag_id', $tag->id)->exists()){
                        $news_tags = new NewsTags();
                        $news_tags->news_id = $news->id;
                        $news_tags->tag_id = $tag->id;
                        $news_tags->save();
                        Log::info('Loop 9 added tag : '.$tag_name);
                    }
                }
                // extract_keywords($html, $keywords);
                Log::info('Loop 8 Tags to assign : '.implode(",",$tags_to_assign));
                
                return 1;
            }
        }
            return 0;
        
    }
}
