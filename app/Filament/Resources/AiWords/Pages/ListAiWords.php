<?php

namespace App\Filament\Resources\AiWords\Pages;

use App\Filament\Resources\AiWords\AiWordsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiWords extends ListRecords
{
    protected static string $resource = AiWordsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
