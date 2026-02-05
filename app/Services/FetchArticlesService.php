<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Models\Source;
use App\Models\SourceFeed;
use App\Models\News;
use App\Models\Tag;
use App\Models\Keyword;
use App\Models\Country;
use Illuminate\Support\Facades\Log;

class FetchArticlesService
{
    protected $client;
    protected $tagsMap = null;
    protected $keywordsMap = null;

    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'application/rss+xml, application/xml, text/xml, */*',
            ]
        ]);
    }

    public function fetch($categoryId = null, $sourceId = null, $sourcefeedId = null, $dryRun = false)
    {
        // Pre-load tags and keywords if not just a dry run (or even if dry run, to show what WOULD be tagged)
        // We do it once per instantiation/fetch call to avoid DB hits per item
        if ($this->tagsMap === null) {
            $this->tagsMap = Tag::pluck('id', 'tag_name')->toArray();
        }
        if ($this->keywordsMap === null) {
            $this->keywordsMap = Keyword::pluck('id', 'keyword_name')->toArray();
        }

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
                $statusCode = $response->getStatusCode();

                if ($statusCode === 403 || $statusCode === 503) {
                    $source_result['status'] = 'error';
                    $source_result['message'] = "Blocked/Unavailable (HTTP $statusCode)";
                    Log::warning("Feed blocked: $url (HTTP $statusCode)");
                    $results[] = $source_result;
                    continue; 
                }

                if ($statusCode !== 200) {
                     $source_result['status'] = 'error';
                     $source_result['message'] = "HTTP $statusCode";
                     $results[] = $source_result;
                     continue;
                }

                $data = $response->getBody()->getContents();
                $data = trim($data);

                if (empty($data)) {
                    throw new \Exception('Empty response body');
                }

                libxml_use_internal_errors(true);
                $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);

                if ($xml === false) {
                     $errors = libxml_get_errors();
                     $errorMsg = 'Invalid XML';
                     if (count($errors) > 0) {
                         $errorMsg .= ': ' . $errors[0]->message;
                     }
                     libxml_clear_errors();
                     
                     $source_result['status'] = 'error';
                     $source_result['message'] = $errorMsg;
                     
                     if (!$dryRun) {
                        SourceFeed::where('source_url', $url)->update(['status_id' => '0']);
                     }
                } else {
                     $source_result['items'] = $this->processFeed($xml, $rss_url, $dryRun);
                     
                     if (!$dryRun) {
                        SourceFeed::where('source_url', $url)->update(['status_id' => '1']);
                     }
                }

            } catch (\Exception $e) {
                 $source_result['status'] = 'error';
                 $source_result['message'] = $e->getMessage();
                 $results[] = $source_result;
                 continue;
            }
            
            $results[] = $source_result;
        }

        return $results;
    }

    protected function processFeed($xml, $rss_url, $dryRun)
    {
        $rootName = strtolower($xml->getName());
        $items = [];
        $source_id = $rss_url->source->id;
        $category_id = $rss_url->category_id;
        
        if ($rootName === 'feed') {
            $entries = $xml->entry;
        } elseif ($rootName === 'rss' || $rootName === 'channel') {
            $entries = $xml->channel->item ?? [];
        } else {
            $entries = $xml->item ?? [];
        }

        foreach ($entries as $entry) {
            $parsedItem = $this->parseItem($entry, $rootName);
            
            $link = $this->extractCleanUrl($parsedItem['link']);
            $title = Str::limit($parsedItem['title'], 250);
            
            $url2 = Str::limit(Str::slug($title, '-'), 50);
            if (empty($url2)) {
                $url2 = 'news-' . uniqid();
            }

            $date = $parsedItem['date'] ?? date('Y-m-d H:i:s');
            $post_status = 'New';
            $matchedTags = [];
            $matchedKeywords = [];

            if ($dryRun) {
                // Dry run logic
                $exists = News::where('source_link', '=', $link)->exists();
                if ($exists) {
                    $post_status = 'Existing (Skipped in Dry Run)';
                } else {
                    $post_status = 'New (Would Create)';
                }
                
                // Simulate Matching
                if (!empty($parsedItem['description'])) {
                    $contentForMatching = (string)$title . ' ' . (string)$parsedItem['description'];
                    $matchedTags = $this->simulateMatch($contentForMatching, $this->tagsMap);
                    $matchedKeywords = $this->simulateMatch($contentForMatching, $this->keywordsMap);
                }

            } else {
                // Real Execution
                $exists = News::where('source_link', '=', $link)->exists();
                
                if ($exists) {
                    $post_status = 'Already exist';
                } else {
                    try {
                        $description = $parsedItem['description'];
                        
                        $post = new News();
                        $post->name = (string)$title;
                        $post->slug = $url2;
                        $post->content = (string)$description;
                        $post->date =  $date;
                        $post->category_id = $category_id;
                        $post->source_link = $link;
                        $post->excerpt  = Str::limit(strip_tags(html_entity_decode((string)$description)), 100);
                        $post->image = $parsedItem['image'];
                        $post->run_cron  ="0";
                        $post->video = $this->extractVideo((string)$description);
                        $post->source_id  = $source_id;
                        $post->sourcefeed_id = $rss_url->id;
                        $post->created_at = $date;
                        $post->updated_at = $date; 
                        
                        $post->save();

                        $source = Source::find($source_id);
                        if ($source) {
                            $post->countries()->attach($source->countries->pluck('id'));
                        }

                        // Tags & Keywords
                        $this->matchAndAttach($post, $this->tagsMap, 'tags');
                        $this->matchAndAttach($post, $this->keywordsMap, 'keywords'); // Assumes relation name 'keywords' exists on News

                        $post_status = 'New';
                    } catch (\Exception $ex) {
                        $post_status = 'Error: ' . $ex->getMessage();
                        Log::error("Error saving news item: " . $ex->getMessage());
                    }
                }
            }

            $items[] = [
                'title' => (string)$title,
                'date' => $date,
                'status' => $post_status,
                'link' => $link, // Added for UI
                'tags_count' => count($matchedTags),
                'keywords_count' => count($matchedKeywords),
            ];
        }
        
        return $items;
    }

    protected function parseItem($entry, $type)
    {
        $namespaces = $entry->getNamespaces(true);
        $data = [
            'title' => '',
            'link' => '',
            'description' => '',
            'date' => null,
            'image' => null,
        ];

        $data['title'] = (string)$entry->title;

        if ($type === 'feed') { 
            if (isset($entry->link)) {
                foreach ($entry->link as $l) {
                    $attr = $l->attributes();
                    if (isset($attr['rel']) && (string)$attr['rel'] == 'alternate') {
                        $data['link'] = (string)$attr['href'];
                        break;
                    }
                    if (!isset($attr['rel'])) {
                         $data['link'] = (string)$attr['href'];
                    }
                }
            }
        } else {
            $data['link'] = (string)$entry->link;
        }

        if (isset($entry->description)) {
            $data['description'] = (string)$entry->description;
        } elseif (isset($namespaces['content']) && isset($entry->children($namespaces['content'])->encoded)) {
            $data['description'] = (string)$entry->children($namespaces['content'])->encoded;
        } elseif (isset($entry->content)) {
            $data['description'] = (string)$entry->content;
        }

        $dateStr = null;
        if (isset($entry->pubDate)) { 
            $dateStr = (string)$entry->pubDate;
        } elseif (isset($namespaces['dc']) && isset($entry->children($namespaces['dc'])->date)) { 
            $dateStr = (string)$entry->children($namespaces['dc'])->date;
        } elseif (isset($entry->updated)) { 
            $dateStr = (string)$entry->updated;
        } elseif (isset($entry->published)) { 
            $dateStr = (string)$entry->published;
        }

        try {
            if ($dateStr) {
                $data['date'] = \Carbon\Carbon::parse($dateStr)->format('Y-m-d H:i:s');
            } else {
                $data['date'] = date('Y-m-d H:i:s'); 
            }
        } catch (\Exception $e) {
            $data['date'] = date('Y-m-d H:i:s'); 
        }

        $data['image'] = $this->extractImage($entry, $data['description'], $namespaces);

        return $data;
    }

    protected function extractImage($entry, $content, $namespaces)
    {
        if (isset($entry->enclosure)) {
            $attr = $entry->enclosure->attributes();
            if (isset($attr['type']) && strpos((string)$attr['type'], 'image') !== false && isset($attr['url'])) {
                return (string)$attr['url'];
            }
        }

        if (isset($namespaces['media'])) {
            $media = $entry->children($namespaces['media']);
            if (isset($media->content)) {
                 $attr = $media->content->attributes();
                 if (isset($attr['url']) && (!isset($attr['medium']) || $attr['medium'] == 'image')) {
                     return (string)$attr['url'];
                 }
            }
            if (isset($media->thumbnail)) {
                $attr = $media->thumbnail->attributes();
                if (isset($attr['url'])) {
                    return (string)$attr['url'];
                }
            }
            if (isset($media->group)) {
                 if (isset($media->group->content)) {
                     $attr = $media->group->content->attributes();
                     if (isset($attr['url'])) {
                         return (string)$attr['url'];
                     }
                 }
                 if (isset($media->group->thumbnail)) {
                     $attr = $media->group->thumbnail->attributes();
                     if (isset($attr['url'])) {
                         return (string)$attr['url'];
                     }
                 }
            }
        }

        if (preg_match('/<img[^>]+src="([^">]+)"/i', $content, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function matchAndAttach($post, $map, $relation)
    {
        $content = $post->name . ' ' . $post->content;
        $ids = [];
        
        foreach ($map as $name => $id) {
            // Case insensitive check
            if (stripos($content, $name) !== false) {
                 $ids[] = $id;
            }
        }
        
        if (!empty($ids)) {
            $post->$relation()->attach($ids);
        }
    }

    protected function simulateMatch($content, $map)
    {
        $ids = [];
        foreach ($map as $name => $id) {
            if (stripos($content, $name) !== false) {
                 $ids[] = $id;
            }
        }
        return $ids;
    }

    protected function matchTags($post)
    {
        // Wrapper for compatibility if necessary, but logic is moved to matchAndAttach
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
