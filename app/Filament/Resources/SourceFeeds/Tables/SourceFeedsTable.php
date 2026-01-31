<?php

namespace App\Filament\Resources\SourceFeeds\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class SourceFeedsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('filament.id'))
                    ->searchable(),
                TextColumn::make('source_url')->label(__('filament.source_url'))
                    ->searchable(),
                TextColumn::make('source.arabic_name')->label(__('filament.source'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category.arabic_name')->label(__('filament.category'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status_id')->label(__('filament.status_id'))->label(__('filament.valid_url'))
                    ->boolean(),
                ToggleColumn::make('freeze')->label(__('filament.freeze')),
                TextColumn::make('created_at')->label(__('filament.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('filament.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
            
                SelectFilter::make('source')->label(__('filament.source'))
                    ->relationship('source', 'arabic_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category')->label(__('filament.category'))
                    ->relationship('category', 'arabic_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status_id')->label(__('filament.status_id'))
                    ->label(__('filament.status'))
                    ->options([
                        1 => 'Valid',
                        0 => 'Invalid',
                    ]),
            ],  layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('testFeed')
                    ->label(__('filament.test_feed'))
                    ->icon('heroicon-s-play')
                    ->button()
                    ->outlined()
                    ->color('zinc')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament.close'))
                    ->modalContent(function (\App\Models\SourceFeed $record) {
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
                EditAction::make()->button()->color('zinc'),
                DeleteAction::make()->button()->color('danger'),
                
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
