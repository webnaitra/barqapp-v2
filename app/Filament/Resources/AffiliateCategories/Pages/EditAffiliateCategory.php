<?php

namespace App\Filament\Resources\AffiliateCategories\Pages;

use App\Filament\Resources\AffiliateCategories\AffiliateCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateCategory extends EditRecord
{
    protected static string $resource = AffiliateCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
