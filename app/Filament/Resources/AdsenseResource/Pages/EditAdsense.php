<?php

namespace App\Filament\Resources\AdsenseResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AdsenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdsense extends EditRecord
{
    protected static string $resource = AdsenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
