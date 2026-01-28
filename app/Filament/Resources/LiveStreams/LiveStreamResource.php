<?php

namespace App\Filament\Resources\LiveStreams;

use Filament\Schemas\Schema;
use App\Filament\Resources\LiveStreams\Pages\CreateLiveStream;
use App\Filament\Resources\LiveStreams\Pages\EditLiveStream;
use App\Filament\Resources\LiveStreams\Pages\ListLiveStreams;
use App\Filament\Resources\LiveStreams\Schemas\LiveStreamForm;
use App\Filament\Resources\LiveStreams\Tables\LiveStreamsTable;
use App\Models\LiveStream;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveStreamResource extends Resource
{
    protected static ?string $model = LiveStream::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tv';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.videos_management');
    }

        public static function getModelLabel(): string
    {
        return __('filament.live_stream');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.live_streams');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return LiveStreamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveStreamsTable::configure($table);
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
            'index' => ListLiveStreams::route('/'),
            'create' => CreateLiveStream::route('/create'),
            'edit' => EditLiveStream::route('/{record}/edit'),
        ];
    }
}
