<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Str;


class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->label(__('filament.slug')),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()->columnSpan(2),
                Textarea::make('excerpt')->label(__('filament.excerpt'))
                    ->required()
                    ->columnSpan(2),
                RichEditor::make('content')->label(__('filament.content'))
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('category_id')->label(__('filament.category_id'))
                    ->relationship(name: 'category', titleAttribute: 'arabic_name'),
                Select::make('source_id')->label(__('filament.source_id'))
                    ->relationship(name: 'sources', titleAttribute: 'arabic_name')
                    ->label(__('filament.source')),
                Select::make('keywords')->label(__('filament.keywords'))
                    ->relationship(name: 'keywords', titleAttribute: 'keyword_name')
                    ->multiple()
                    ->preload()
                    ->label(__('filament.keywords')),
                Select::make('tags')->label(__('filament.tags'))
                    ->relationship(name: 'tags', titleAttribute: 'tag_name')
                    ->multiple()
                    ->preload()
                    ->label(__('filament.tags')),
                DatePicker::make('date')->label(__('filament.date'))
                    ->default(null),
                TextInput::make('sourcefeed_id')->label(__('filament.sourcefeed_id'))
                    ->numeric()
                    ->default(null),
                TextInput::make('views')->label(__('filament.views'))
                    ->numeric()
                    ->default(0),
                TextInput::make('shares')->label(__('filament.shares'))
                    ->numeric()
                    ->default(0),
                TextInput::make('likes')->label(__('filament.likes'))
                    ->numeric()
                    ->default(0),
                Toggle::make('urgent')->label(__('filament.urgent'))
                    ->label(__('filament.trending'))
                    ->default(0),
                TextInput::make('source_link')->label(__('filament.source_link'))
                    ->default(null)
                    ->maxLength(255)
                    ->url(),
                Toggle::make('run_cron')->label(__('filament.run_cron'))->label(__('filament.processing_pending')),
                Toggle::make('is_updated')->label(__('filament.is_updated'))->label(__('filament.processing_complete')),
            ]);
    }
}
