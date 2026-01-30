<?php

namespace App\Filament\Resources\Keywords\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class KeywordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('keyword_name')->label(__('filament.keyword_name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2)
                    ->label(__('filament.keyword_name')),
                Select::make('category_id')->label(__('filament.category_id'))
                    ->relationship('category', 'arabic_name')
                    ->required()
                    ->columnSpan(2)
                    ->label(__('filament.category')),
                Select::make('country_id')->label(__('filament.country_id'))
                    ->multiple()
                    ->relationship('countries', 'arabic_name')
                    ->required()
                    ->columnSpan(2)
                    ->preload()
                    ->label(__('filament.country')),
                TextInput::make('short_description')->label(__('filament.short_description'))
                    ->maxLength(255)
                    ->columnSpan(2)
                    ->label(__('filament.short_description')),
                Textarea::make('description')->label(__('filament.description'))
                    ->columnSpan(2)
                    ->label(__('filament.description')),
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
