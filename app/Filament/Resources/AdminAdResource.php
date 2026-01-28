<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AdminAdResource\Pages\ListAdminAds;
use App\Filament\Resources\AdminAdResource\Pages\CreateAdminAd;
use App\Filament\Resources\AdminAdResource\Pages\EditAdminAd;
use App\Filament\Resources\AdminAdResource\Pages;
use App\Filament\Resources\AdminAdResource\RelationManagers;
use App\Models\AdminAd;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

class AdminAdResource extends Resource
{
    protected static ?string $model = AdminAd::class;

    protected static string | \BackedEnum | null $navigationIcon  = 'heroicon-o-swatch';
    protected static string | \UnitEnum | null $navigationGroup = 'Ads Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->maxLength(255)
                    ->default(null)->columnSpan(2),
                FileUpload::make('image')
                    ->image()
                    ->default(null),
                FileUpload::make('source_icon')
                    ->image()
                    ->default(null),
                TextInput::make('fav_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('share_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('url')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('help_text')
                    ->maxLength(255)
                    ->default(null),
                Select::make('type')
                    ->options([
                        'full' => 'Full',
                        'column' => 'Column',
                    ])
                    ->default(null),
                Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                Select::make('locations')
                    ->relationship('locations', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                SelectColumn::make('type')
                    ->options([
                        'full' => 'Full',
                        'column' => 'Column',
                    ]),
                TextColumn::make('categories.name')
                    ->sortable(),
                TextColumn::make('locations.name')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                ->options([
                    'full' => 'Full',
                    'column' => 'Column',
                ]),
                SelectFilter::make('locations')
                    ->relationship('locations', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload(),

            ], FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()->button()->outlined(),
                DeleteAction::make()->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListAdminAds::route('/'),
            'create' => CreateAdminAd::route('/create'),
            'edit' => EditAdminAd::route('/{record}/edit'),
        ];
    }
}
