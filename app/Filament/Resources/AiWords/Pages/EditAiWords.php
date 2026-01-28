<?php

namespace App\Filament\Resources\AiWords\Pages;

use App\Filament\Resources\AiWords\AiWordsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiWords extends EditRecord
{
    protected static string $resource = AiWordsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
