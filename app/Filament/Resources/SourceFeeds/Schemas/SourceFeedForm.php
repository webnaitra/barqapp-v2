<?php

namespace App\Filament\Resources\SourceFeeds\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

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
                TextInput::make('source_id')->label(__('filament.source_id'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('category_id')->label(__('filament.category_id'))
                    ->numeric()
                    ->default(null),
                Toggle::make('status_id')->label(__('filament.status_id'))
                    ->required(),
                Toggle::make('freeze')->label(__('filament.freeze'))
                    ->required(),
            ]);
    }
}
