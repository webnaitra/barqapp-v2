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
            DeleteAction::make(),
        ];
    }
}
