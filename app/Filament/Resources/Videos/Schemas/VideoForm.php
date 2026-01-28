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
                TextInput::make('name')
                    ->default(null),
                FileUpload::make('image')
                    ->image()
                    ->default(null)
                    ->columnSpan(2),
                Select::make('sources')
                    ->relationship(titleAttribute: 'arabic_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category')
                    ->relationship(titleAttribute: 'arabic_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('countries')
                    ->relationship(titleAttribute: 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->columnSpan(2)
                    ->required(),
                TextInput::make('video')
                    ->default(null),
                TextInput::make('source_link')
                    ->default(null)
                    ->url(),
            ]);
    }
}
