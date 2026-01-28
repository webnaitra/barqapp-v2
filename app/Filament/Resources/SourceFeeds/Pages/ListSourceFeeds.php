<?php

namespace App\Filament\Resources\SourceFeeds\Pages;

use App\Filament\Resources\SourceFeeds\SourceFeedResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSourceFeeds extends ListRecords
{
    protected static string $resource = SourceFeedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
