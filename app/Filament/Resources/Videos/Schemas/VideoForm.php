<?php

namespace App\Filament\Resources\Videos\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->default(null),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
                    ->default(null)
                    ->columnSpan(2),
                Select::make('category_id')->label(__('filament.category_id'))
                    ->relationship(name: 'category', titleAttribute: 'arabic_name')
                    ->label(__('filament.category'))
                    ->preload()
                    ->required(),
                Select::make('source_id')->label(__('filament.source_id'))
                    ->relationship(name: 'sources', titleAttribute: 'arabic_name')
                    ->label(__('filament.source'))
                    ->preload()
                    ->required(),
                Select::make('countries')->label(__('filament.countries'))
                    ->relationship(name: 'countries', titleAttribute: 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->columnSpan(2)
                    ->required(),
                TextInput::make('video')->label(__('filament.video'))
                    ->default(null),
                TextInput::make('source_link')->label(__('filament.source_link'))
                    ->default(null)
                    ->url(),
            ]);
    }
}
