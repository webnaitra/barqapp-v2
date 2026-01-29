<?php

namespace App\Filament\Resources\LiveStreams\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class LiveStreamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->default(null)
                    ->maxLength(255)
                    ->columnSpan('full')
                    ->required(),
                Textarea::make('description')->label(__('filament.description'))
                    ->default(null)
                    ->columnSpan('full'),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
                    ->columnSpan('full')
                    ->required(),
                TextInput::make('video')->label(__('filament.video'))
                    ->default(null)
                    ->url()
                    ->columnSpan('full')
                    ->required(),
                TextInput::make('url')->label(__('filament.url'))
                    ->url()
                    ->default(null)
                    ->columnSpan('full')
                    ->required(),
                Select::make('countries')->label(__('filament.countries'))
                    ->relationship('countries', 'arabic_name')
                    ->multiple()
                    ->preload()
                    ->label(__('filament.country'))
                    ->columnSpan('full')
                    ->required(),
            ]);
    }
}
