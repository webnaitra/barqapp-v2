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
    public static function getNavigationGroup(): ?string
    {
        return __('filament.ads_management');
    }

    public static function getModelLabel(): string
    {
        return __('filament.advertisement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.advertisements');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->maxLength(255)
                    ->default(null)->columnSpan(2),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->default(null),
                FileUpload::make('source_icon')->label(__('filament.source_icon'))
                    ->image()
                    ->default(null),
                TextInput::make('fav_count')->label(__('filament.fav_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('view_count')->label(__('filament.view_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('share_count')->label(__('filament.share_count'))
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('url')->label(__('filament.url'))
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('help_text')->label(__('filament.help_text'))
                    ->maxLength(255)
                    ->default(null),
                Select::make('type')->label(__('filament.type'))
                    ->options([
                        'full' => 'Full',
                        'column' => 'Column',
                    ])
                    ->default(null),
                Select::make('categories')->label(__('filament.categories'))
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                Select::make('locations')->label(__('filament.locations'))
                    ->relationship('locations', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament.name'))
                    ->searchable(),
                SelectColumn::make('type')->label(__('filament.type'))
                    ->options([
                        'full' => 'Full',
                        'column' => 'Column',
                    ]),
                TextColumn::make('categories.name')->label(__('filament.categoriesname'))
                    ->sortable(),
                TextColumn::make('locations.name')->label(__('filament.locationsname'))
                    ->sortable(),
                TextColumn::make('created_at')->label(__('filament.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('filament.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->label(__('filament.type'))
                ->options([
                    'full' => 'Full',
                    'column' => 'Column',
                ]),
                SelectFilter::make('locations')->label(__('filament.locations'))
                    ->relationship('locations', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('categories')->label(__('filament.categories'))
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
