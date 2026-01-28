<?php

namespace App\Filament\Resources\AdminAdResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AdminAdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminAd extends EditRecord
{
    protected static string $resource = AdminAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
