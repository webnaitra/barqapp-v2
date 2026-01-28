<?php

namespace App\Filament\Resources\AdminAdResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AdminAdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminAds extends ListRecords
{
    protected static string $resource = AdminAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
