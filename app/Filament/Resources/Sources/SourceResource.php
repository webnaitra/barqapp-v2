<?php

namespace App\Filament\Resources\Sources;

use Filament\Schemas\Schema;
use App\Filament\Resources\Sources\Pages\CreateSource;
use App\Filament\Resources\Sources\Pages\EditSource;
use App\Filament\Resources\Sources\Pages\ListSources;
use App\Filament\Resources\Sources\Schemas\SourceForm;
use App\Filament\Resources\Sources\Tables\SourcesTable;
use App\Models\Source;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SourceResource extends Resource
{
    protected static ?string $model = Source::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $recordTitleAttribute = 'name';
    protected static string | \UnitEnum | null $navigationGroup = 'Source Management';

    public static function form(Schema $schema): Schema
    {
        return SourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SourcesTable::configure($table);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
            'index' => ListSources::route('/'),
            'create' => CreateSource::route('/create'),
            'edit' => EditSource::route('/{record}/edit'),
        ];
    }
}
