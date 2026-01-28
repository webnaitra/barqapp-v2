<?php

namespace App\Filament\Resources\SourceFeeds;

use Filament\Schemas\Schema;
use App\Filament\Resources\SourceFeeds\Pages\CreateSourceFeed;
use App\Filament\Resources\SourceFeeds\Pages\EditSourceFeed;
use App\Filament\Resources\SourceFeeds\Pages\ListSourceFeeds;
use App\Filament\Resources\SourceFeeds\Schemas\SourceFeedForm;
use App\Filament\Resources\SourceFeeds\Tables\SourceFeedsTable;
use App\Models\SourceFeed;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SourceFeedResource extends Resource
{
    protected static ?string $model = SourceFeed::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rss';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.source_management');
    }

        public static function getModelLabel(): string
    {
        return __('filament.source_feed');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.source_feeds');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SourceFeedForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SourceFeedsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSourceFeeds::route('/'),
            'create' => CreateSourceFeed::route('/create'),
            'edit' => EditSourceFeed::route('/{record}/edit'),
        ];
    }
}
