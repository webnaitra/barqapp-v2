<?php

namespace App\Filament\Resources\AdvertiserResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AdvertiserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvertiser extends EditRecord
{
    protected static string $resource = AdvertiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
