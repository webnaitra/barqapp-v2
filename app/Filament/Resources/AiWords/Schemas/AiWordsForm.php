<?php

namespace App\Filament\Resources\AiWords\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Select;

class AiWordsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TagsInput::make('words')->label(__('filament.words'))
                    ->separator(',')
                    ->columnSpan(2)
                    ->required(),
                Select::make('category_id')->label(__('filament.category_id'))
                    ->relationship(name: 'category', titleAttribute: 'name')->required(),
                Select::make('tags')->label(__('filament.tags'))
                    ->relationship(name: 'tags', titleAttribute: 'tag_name')
                    ->multiple()
                    ->preload()
                    ->label(__('filament.tags'))
                    ->required(),
            ]);
    }
}
