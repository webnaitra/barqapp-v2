<?php

namespace App\Filament\Resources\AdvertiserResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AdvertiserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdvertisers extends ListRecords
{
    protected static string $resource = AdvertiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
