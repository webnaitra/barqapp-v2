<?php

namespace App\Filament\Resources\AdminNotificationResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\AdminNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminNotifications extends ListRecords
{
    protected static string $resource = AdminNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
