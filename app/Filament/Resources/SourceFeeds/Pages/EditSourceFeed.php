<?php

namespace App\Filament\Resources\SourceFeeds\Pages;

use App\Filament\Resources\SourceFeeds\SourceFeedResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSourceFeed extends EditRecord
{
    protected static string $resource = SourceFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('testFeed')
                ->label(__('filament.test_feed'))
                ->icon('heroicon-o-play')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('filament.close'))
                ->modalContent(function ($record) {
                    $url = $record->source_url;
                    $items = [];
                    $error = null;
                    
                    $client = new \GuzzleHttp\Client();
                    try {
                        $response = $client->get($url, ['verify' => false]);
                        if ($response->getStatusCode() == 200) {
                            $data = $response->getBody()->getContents();
                            $data = trim($data);
                            $xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
                            
                            if ($xml && isset($xml->channel->item)) {
                                foreach ($xml->channel->item as $item) {
                                    $items[] = [
                                        'title' => (string)$item->title,
                                        'date' => (string)$item->pubDate,
                                        'link' => (string)$item->link,
                                    ];
                                }
                            }
                        } else {
                            $error = 'Error: ' . $response->getStatusCode();
                        }
                    } catch (\Exception $e) {
                        $error = $e->getMessage();
                    }

                    return view('filament.components.feed-preview', [
                        'items' => $items,
                        'error' => $error,
                    ]);
                }),
            DeleteAction::make(),
        ];

    }
}
