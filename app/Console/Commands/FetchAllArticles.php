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

use App\Models\SourceFeed;
use App\Models\Source;
use App\Models\News;
use App\Models\News_Category;
use App\Models\Current_Tags;
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
     * Extract video from an article if it exists
     */

     public function extractVideo($content){
      preg_match_all(
        '@(https?://)?(?:www\.)?(youtu(?:\.be/([-\w]+)|be\.com/watch\?v=([-\w]+)))\S*@im',
        $content,
        $matches
      );
      return isset($matches[0][0]) ? $matches[0][0] : null;
    }

    /**
     *  Extract clean source url
     */

    function extractCleanUrl($linkContent) {
    // First, URL decode the content
        $decoded = urldecode($linkContent);
        
        // Check if it contains an anchor tag with href
        if (preg_match('/href=["\']([^"\']+)["\']/', $decoded, $matches)) {
            // Extract the URL from href attribute
            $url = $matches[1];
            
            // Decode the URL (it might be double or triple encoded)
            while (strpos($url, '%') !== false && $url !== urldecode($url)) {
                $url = urldecode($url);
            }
            
            return $url;
        }
        
        // If no href found, just decode and return
        $url = $decoded;
        while (strpos($url, '%') !== false && $url !== urldecode($url)) {
            $url = urldecode($url);
        }
        
        return $url;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      ini_set("implicit_flush", "1");
      $client = new Client();
      $showOutput = $this->argument('showOutput');
      $categoryId = $this->argument('categoryId');
      $sourceId = $this->argument('sourceId');
      $sourcefeedId = $this->argument('sourcefeedId');

      $rss_urls = SourceFeed::whereHas('source', function($query) {
          $query->select('name','source_url','category_id','source_id');
      });

      if(!empty($categoryId) && $categoryId != 'null'){
          $rss_urls = $rss_urls->where('category_id',$categoryId);
      }

      if(!empty($sourceId) && $sourceId != 'null'){
          $rss_urls = $rss_urls->where('source_id',$sourceId);
      }

      if(!empty($sourcefeedId) && $sourcefeedId != 'null'){
          $rss_urls = $rss_urls->where('id',$sourcefeedId);
      }

      $rss_urls = $rss_urls->with('source')->get();

      $previous_source_name = '';
      foreach ($rss_urls as $rss_url) {

     $url = $rss_url->source_url;
     $post_count = 0;

     if($showOutput){
       ob_start(null, 4096);
       ob_implicit_flush(true);
       echo "<html>
       <head>
       <link rel='stylesheet' href=".asset('vendor/bootstrap.min.css').">
       </head>
       <body>";

 			echo "<br>";
       echo "<table class='table table-sm table-bordered table-striped'>
               <thead class='thead-dark'>
                 <tr>
                   <th>Feed Url</th>
                   <th colspan='3'>".$url."</th>
                 </tr>
               </thead>
               <tbody>
                 <tr>
                   <th width='100px'>#</th>
                   <th width='450px'>Name </th>
                   <th width='300px'>Date</th>
                   <th width='100px'>Status</th>
                 </tr>";
     }


            $source_id = $rss_url->source->id;
			      $description = @$rss_url->source->description;
            $filter_classes = @$rss_url->source->filter_classes;


            $category_id = $rss_url->category_id;

              if(!empty($url) ){
                try {
                  $response = $client->get($url, ['verify' => false, 'http_errors' => false]);
                } catch (\GuzzleHttp\Exception\RequestException $e) {
                  if ($showOutput) {
                    echo "<tr><td colspan='4'>Request error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                  }
                  continue;
                } catch (\Exception $e) {
                  if ($showOutput) {
                    echo "<tr><td colspan='4'>General error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                  }
                  continue;
                }
           if( $response->getStatusCode() == 200 ) {
            $data = $response->getBody()->getContents();
            $data = trim($data);

			      libxml_use_internal_errors(true);
			      $mainrespinse = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

					if ($mainrespinse === false) {
					    echo "This url not working";
					   /* foreach(libxml_get_errors() as $error) {
							echo "\t", $error->message;
						}*/
							SourceFeed::where('source_url', $url)
							->update([
						   'status_id' => '0'
						]);
					}else {
					//echo "working";
            //$mainrespinse = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        $img_source = null;
        $loop_object = is_scalar($mainrespinse->channel->item) ? [] : $mainrespinse->channel->item;
        foreach ($mainrespinse->channel->item ?? [] as $key=>$item) {
  
            $link = urldecode($item->link);
            $link = $this->extractCleanUrl($link);

            $currnt_date =   date("Y-m-d h:i:s",strtotime("-1 days"));
                // Parse pubDate if present
              $publishedAt = null;
              if (isset($item->pubDate) && !empty($item->pubDate) && strtotime($item->pubDate) !== false) {
                  $publishedAt = \Carbon\Carbon::parse((string)$item->pubDate);
              }
              
              // Parse enclosure image if present
              $imageUrl = null;
              if (isset($item->enclosure)) {
                  $enclosure = $item->enclosure->attributes();
                  if (isset($enclosure['url']) && $enclosure['type'] == 'image/jpeg') {
                      $imageUrl = (string)$enclosure['url'];
                  }
              }


           // $date = date("Y-m-d h:i:s", strtotime($item->pubDate));
			      $date = date("Y-m-d h:i:s");
            if($date >= $currnt_date ){
            $url2 = Str::limit(Str::slug($item->title, '-'), 50);
      
            $details = News::select('id')
                    ->WHERE('source_link','=',$link)
                    ->get();

                $post_title = $item->title;
                $post_status = '';

            if(count($details) > 0){
                $post_status = " Already exist! ";
             } else {
                 
                try {



                  $post = new News();
                  $post->name = $item->title;
                  $post->slug = trim($url2);
                  $post->content = $item->description;
                  $post->date =  $date;
                  $post->category_id = $category_id;
                  $post->source_link = $link;
                  $post->excerpt  = Str::limit(strip_tags(html_entity_decode($item->description)), 100);
                  $post->image = $imageUrl;
                  $post->run_cron  ="0";
                  $post->video = $this->extractVideo($item->description);
                  $post->source_id  = $source_id;
                  $post->sourcefeed_id = $rss_url->id;
                  $post->created_at = $publishedAt ?? $date;
                  $post->updated_at = $date;
                  $post->save();
                  if($post->save()){
                     $tags = Current_Tags::get();
                     $news = \App\Models\News::where('id',$post->id)->first();

                        foreach($tags as $tag){
                            
                            
                            if(str_contains($news->news_title,$tag->tag_name) || str_contains($news->news_content,$tag->tag_name)  ){
                                    $news_tags = new NewsTags();
                                    $news_tags->news_id = $post->id;
                                    $news_tags->tag_id = $tag->id;
                                    $news_tags->save();
                                    
                            }
                            
                        }
                      
                      
                  }
                    
                  $post_status = "New";

                } catch ( \Illuminate\Database\QueryException $ex) {
                   $post_status = "Error";
                  dd($ex);
                }

                  }
                    SourceFeed::where('source_url', $url)
                        ->update([
                       'status_id' => '1'
                    ]);
        }
            if($showOutput){
              $post_count++;
              echo "<tr>
                <td>".($post_count)."</td>
                <td>".$post_title."</td>
                <td>".$date."</td>
                <td>".$post_status."</td>
              </tr>";
            }
           }
					}
      } else {
                if($showOutput){
                  echo "<tr><td colspan='3'> No Data found! </td></tr>";
                }
            }
         }

         if($showOutput){
           echo  "
           </tbody>
           </table>
           </body>
           </html>
           ";

           ob_flush();
           ob_end_clean();
         }
      }
        return 0;
    }
}
