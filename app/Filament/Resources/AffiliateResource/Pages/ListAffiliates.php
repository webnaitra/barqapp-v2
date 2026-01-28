<?php

namespace App\Filament\Resources\AffiliateResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AffiliateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAffiliates extends ListRecords
{
    protected static string $resource = AffiliateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
