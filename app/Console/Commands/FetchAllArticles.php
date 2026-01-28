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
    public function handle(FetchArticlesService $service)
    {
      $showOutput = $this->argument('showOutput');
      $categoryId = $this->argument('categoryId');
      $sourceId = $this->argument('sourceId');
      $sourcefeedId = $this->argument('sourcefeedId');

      $results = $service->fetch($categoryId, $sourceId, $sourcefeedId);

      if ($showOutput) {
          foreach ($results as $result) {
              $this->info("Feed: " . $result['feed_url']);
              if($result['status'] == 'error'){
                  $this->error("Error: " . $result['message']);
                  continue;
              }
              
              $headers = ['#', 'Name', 'Date', 'Status'];
              $rows = [];
              foreach (($result['items'] ?? []) as $index => $item) {
                  $rows[] = [
                      $index + 1,
                      Str::limit($item['title'], 50),
                      $item['date'],
                      $item['status']
                  ];
              }
              $this->table($headers, $rows);
          }
      }

      return 0;
    }
}
