<?php

namespace App\Filament\Resources\SourceFeeds\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class SourceFeedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->default(null),
                TextInput::make('source_url')->label(__('filament.source_url'))
                    ->url()
                    ->default(null),
                Select::make('category_id')->label(__('filament.category_id'))
                    ->relationship(name: 'category', titleAttribute: 'arabic_name'),
                Select::make('source_id')->label(__('filament.source_id'))
                    ->relationship(name: 'source', titleAttribute: 'arabic_name')
                    ->label(__('filament.source')),
                Toggle::make('status_id')->label(__('filament.status_id'))
                    ->required(),
                Toggle::make('freeze')->label(__('filament.freeze'))
                    ->required(),
            ]);
    }
}
