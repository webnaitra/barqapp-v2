<?php

namespace App\Filament\Resources\AdsenseResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AdsenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdsenses extends ListRecords
{
    protected static string $resource = AdsenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
