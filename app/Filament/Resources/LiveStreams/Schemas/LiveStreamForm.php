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
                TextInput::make('name')
                    ->default(null)
                    ->maxLength(255)
                    ->columnSpan('full'),
                TextInput::make('description')
                    ->default(null)
                    ->maxLength(255)
                    ->columnSpan('full'),
                FileUpload::make('image')
                    ->image()
                    ->columnSpan('full'),
                TextInput::make('video')
                    ->default(null)
                    ->url()
                    ->columnSpan('full'),
                TextInput::make('url')
                    ->url()
                    ->default(null)
                    ->columnSpan('full'),
                Select::make('countries')
                    ->relationship('countries', 'arabic_name')
                    ->multiple()
                    ->preload()
                    ->label('Country')
                    ->columnSpan('full'),
            ]);
    }
}
