<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['editor_image_url'])) {
            $data['image'] = $data['editor_image_url'];
            unset($data['editor_image_url']);
        }

        return $data;
    }
}
