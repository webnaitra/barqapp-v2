<?php

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tag_name')->label(__('filament.tag_name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2)
                    ->label(__('filament.name')),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
                    ->default(null)
                    ->columnSpan(2)
                    ->label(__('filament.image')),
            ]);
    }
}
