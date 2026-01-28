<?php

namespace App\Filament\Resources\AdminNotificationResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\AdminNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminNotification extends EditRecord
{
    protected static string $resource = AdminNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
