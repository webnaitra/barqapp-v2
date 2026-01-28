<?php

namespace App\Filament\Resources\Keywords\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextArea;

class KeywordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('keyword_name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2)
                    ->label('Keyword Name'),
                Select::make('category_id')
                    ->relationship('category', 'arabic_name')
                    ->required()
                    ->columnSpan(2)
                    ->label('Category'),
                Select::make('country_id')
                    ->multiple()
                    ->relationship('countries', 'arabic_name')
                    ->required()
                    ->columnSpan(2)
                    ->preload()
                    ->label('Country'),
                TextInput::make('short_description')
                    ->maxLength(255)
                    ->columnSpan(2)
                    ->label('Short Description'),
                Textarea::make('description')
                    ->columnSpan(2)
                    ->label('Description'),
                FileUpload::make('image')
                    ->image()
                    ->default(null)
                    ->columnSpan(2)
                    ->label('Image'),
            ]);
    }
}
