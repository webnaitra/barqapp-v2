<?php

namespace App\Filament\Resources\AffiliateCategories\Pages;

use App\Filament\Resources\AffiliateCategories\AffiliateCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateCategories extends ListRecords
{
    protected static string $resource = AffiliateCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
