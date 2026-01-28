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
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug'),
                FileUpload::make('image')
                    ->image()->columnSpan(2),
                Textarea::make('excerpt')
                    ->required()
                    ->columnSpan(2),
                RichEditor::make('content')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->relationship(name: 'category', titleAttribute: 'arabic_name'),
                Select::make('source_id')
                    ->relationship(name: 'sources', titleAttribute: 'arabic_name')
                    ->label('Source'),
                Select::make('keywords')
                    ->relationship(name: 'keywords', titleAttribute: 'keyword_name')
                    ->multiple()
                    ->preload()
                    ->label('Keywords'),
                Select::make('tags')
                    ->relationship(name: 'tags', titleAttribute: 'tag_name')
                    ->multiple()
                    ->preload()
                    ->label('Tags'),
                DatePicker::make('date')
                    ->default(null),
                TextInput::make('sourcefeed_id')
                    ->numeric()
                    ->default(null),
                TextInput::make('views')
                    ->numeric()
                    ->default(0),
                TextInput::make('shares')
                    ->numeric()
                    ->default(0),
                TextInput::make('likes')
                    ->numeric()
                    ->default(0),
                Toggle::make('urgent')
                    ->label('Trending')
                    ->default(0),
                TextInput::make('source_link')
                    ->default(null)
                    ->maxLength(255)
                    ->url(),
                Toggle::make('run_cron')->label('Processing Pending'),
                Toggle::make('is_updated')->label('Processing Complete'),
            ]);
    }
}
