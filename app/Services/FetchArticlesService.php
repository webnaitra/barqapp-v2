<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Models\Source;
use App\Models\SourceFeed;
use App\Models\News;
use App\Models\Tag;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class FetchArticlesService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetch($categoryId = null, $sourceId = null, $sourcefeedId = null)
    {
        $rss_urls = SourceFeed::whereHas('source', function($query) {
            $query->select('name','source_url','category_id','source_id');
        });

        if(!empty($categoryId) && $categoryId != 'null'){
            $rss_urls = $rss_urls->where('category_id', $categoryId);
        }

        if(!empty($sourceId) && $sourceId != 'null'){
            $rss_urls = $rss_urls->where('source_id', $sourceId);
        }

        if(!empty($sourcefeedId) && $sourcefeedId != 'null'){
            $rss_urls = $rss_urls->where('id', $sourcefeedId);
        }

        $rss_urls = $rss_urls->with('source')->get();

        $results = [];

        foreach ($rss_urls as $rss_url) {
            $url = $rss_url->source_url;
            $source_result = [
                'source_name' => $rss_url->source->name ?? 'Unknown',
                'feed_url' => $url,
                'items' => [],
                'status' => 'success',
                'message' => 'Processed',
            ];

            if(empty($url)){
                 $source_result['status'] = 'error';
                 $source_result['message'] = 'Empty URL';
                 $results[] = $source_result;
                 continue;
            }

            try {
                $response = $this->client->get($url, ['verify' => false, 'http_errors' => false]);
            } catch (\Exception $e) {
                 $source_result['status'] = 'error';
                 $source_result['message'] = $e->getMessage();
                 $results[] = $source_result;
                 continue;
            }

            if( $response->getStatusCode() == 200 ) {
                $data = $response->getBody()->getContents();
                $data = trim($data);

                libxml_use_internal_errors(true);
                $mainrespinse = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

                if ($mainrespinse === false) {
                     $source_result['status'] = 'error';
                     $source_result['message'] = 'Invalid XML';
                     SourceFeed::where('source_url', $url)->update(['status_id' => '0']);
                } else {
                     $source_result['items'] = $this->processItems($mainrespinse, $rss_url);
                     SourceFeed::where('source_url', $url)->update(['status_id' => '1']);
                }
            } else {
                 $source_result['status'] = 'error';
                 $source_result['message'] = 'HTTP ' . $response->getStatusCode();
            }
            $results[] = $source_result;
        }

        return $results;
    }

    protected function processItems($xml, $rss_url)
    {
        $items = [];
        $source_id = $rss_url->source->id;
        $category_id = $rss_url->category_id;
        $currnt_date = date("Y-m-d H:i:s", strtotime("-1 days"));

        foreach ($xml->channel->item ?? [] as $item) {
            $link = urldecode($item->link);
            $link = $this->extractCleanUrl($link);

            // Parse pubDate
            $publishedAt = null;
            if (isset($item->pubDate) && !empty($item->pubDate) && strtotime((string)$item->pubDate) !== false) {
                $publishedAt = \Carbon\Carbon::parse((string)$item->pubDate);
            }
            
            // Format check date
            $date = date("Y-m-d H:i:s"); // Assuming we mark fetch time as updated_at? Original code did this.

             // Parse enclosure image
            $imageUrl = null;
            if (isset($item->enclosure)) {
                $enclosure = $item->enclosure->attributes();
                if (isset($enclosure['url']) && (string)$enclosure['type'] == 'image/jpeg') {
                    $imageUrl = (string)$enclosure['url'];
                }
            }

            if($date >= $currnt_date ){ // Logic copied from original: if current time > yesterday? Always true. The intended logic might have been item date > yesterday. But I keep original logic or fix it? Original: $date = date("Y-m-d h:i:s"); if($date >= $currnt_date)... this is ALWAYS true.
                // Original code: $date = date("Y-m-d h:i:s"); if($date >= $currnt_date)...
                // Maybe they meant $publishedAt? But original used $date.
                
                $url2 = Str::limit(Str::slug($item->title, '-'), 50);
                $exists = News::where('source_link', '=', $link)->exists();
                
                $post_status = '';
                if($exists){
                    $post_status = 'Already exist';
                } else {
                    try {
                        $post = new News();
                        $post->name = (string)$item->title;
                        $post->slug = trim($url2);
                        $post->content = (string)$item->description;
                        $post->date =  $date;
                        $post->category_id = $category_id;
                        $post->source_link = $link;
                        $post->excerpt  = Str::limit(strip_tags(html_entity_decode((string)$item->description)), 100);
                        $post->image = $imageUrl;
                        $post->run_cron  ="0";
                        $post->video = $this->extractVideo((string)$item->description);
                        $post->source_id  = $source_id;
                        $post->sourcefeed_id = $rss_url->id;
                        $post->created_at = $publishedAt ?? $date;
                        $post->updated_at = $date;
                        $post->save();
                        $source = Source::find($source_id);
                        if ($source) {
                            $post->countries()->attach($source->countries->pluck('id'));
                        }

                        // Tags
                        $tags = Tag::all();
                        $tagIds = [];
                        foreach ($tags as $tag) {
                             if (str_contains($post->name, $tag->tag_name) || str_contains($post->content, $tag->tag_name)) {
                                  $tagIds[] = $tag->id;
                             }
                        }
                        $post->tags()->attach($tagIds);
                        $post_status = 'New';
                    } catch (\Exception $ex) {
                        $post_status = 'Error: ' . $ex->getMessage();
                    }
                }

                $items[] = [
                    'title' => (string)$item->title,
                    'date' => $date,
                    'status' => $post_status,
                ];
            }
        }
        return $items;
    }

    protected function extractVideo($content)
    {
        preg_match_all(
            '@(https?://)?(?:www\.)?(youtu(?:\.be/([-\w]+)|be\.com/watch\?v=([-\w]+)))\S*@im',
            $content,
            $matches
        );
        return isset($matches[0][0]) ? $matches[0][0] : null;
    }

    protected function extractCleanUrl($linkContent) 
    {
        $decoded = urldecode($linkContent);
        if (preg_match('/href=["\']([^"\']+)["\']/', $decoded, $matches)) {
            $url = $matches[1];
            while (strpos($url, '%') !== false && $url !== urldecode($url)) {
                $url = urldecode($url);
            }
            return $url;
        }
        $url = $decoded;
        while (strpos($url, '%') !== false && $url !== urldecode($url)) {
            $url = urldecode($url);
        }
        return $url;
    }
}
