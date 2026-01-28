<?php

namespace App\Filament\Resources\LiveStreams\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class LiveStreamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->default(null)
                    ->maxLength(255)
                    ->columnSpan('full'),
                TextInput::make('description')->label(__('filament.description'))
                    ->default(null)
                    ->maxLength(255)
                    ->columnSpan('full'),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
                    ->columnSpan('full'),
                TextInput::make('video')->label(__('filament.video'))
                    ->default(null)
                    ->url()
                    ->columnSpan('full'),
                TextInput::make('url')->label(__('filament.url'))
                    ->url()
                    ->default(null)
                    ->columnSpan('full'),
                Select::make('countries')->label(__('filament.countries'))
                    ->relationship('countries', 'arabic_name')
                    ->multiple()
                    ->preload()
                    ->label(__('filament.country'))
                    ->columnSpan('full'),
            ]);
    }
}
