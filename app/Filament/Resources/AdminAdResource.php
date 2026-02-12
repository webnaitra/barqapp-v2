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
                    ->default(null)->columnSpan(2)
                    ->required(),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
                    ->default(null)
                    ->required(),
                 FileUpload::make('source_icon')->label(__('filament.source_icon'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
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
                    ->default(null)
                    ->url()
                    ->required(),
                TextInput::make('help_text')->label(__('filament.help_text'))
                    ->maxLength(255)
                    ->default(null)
                    ->required(),
                Select::make('type')->label(__('filament.type'))
                    ->options([
                        'full' => 'Full',
                        'column' => 'Column',
                        'affiliate-banner' => 'Affiliate Banner',
                        'affiliate-large-banner' => 'Affiliate Large Banner'
                    ])
                    ->default(null)
                    ->required(),
                Select::make('categories')->label(__('filament.categories'))
                    ->relationship('categories', 'arabic_name')
                    ->multiple()
                    ->preload(),
                Select::make('countries')->label(__('filament.countries'))
                    ->relationship('countries', 'arabic_name')
                    ->multiple()
                    ->preload(),
                Select::make('locations')->label(__('filament.locations'))
                    ->relationship('locations', 'name')
                    ->multiple()
                    ->preload()
                    ->required(),
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
                        'affiliate-banner' => 'Affiliate Banner',
                        'affiliate-large-banner' => 'Affiliate Large Banner'
                    ]),
                TextColumn::make('categories.arabic_name')->label(__('filament.categories')),
                TextColumn::make('countries.arabic_name')->label(__('filament.countries')),
                TextColumn::make('locations.name')->label(__('filament.locations')),
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
                   'affiliate-banner' => 'Affiliate Banner',
                   'affiliate-large-banner' => 'Affiliate Large Banner'
                ]),
                SelectFilter::make('locations')->label(__('filament.locations'))
                    ->relationship('locations', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('categories')->label(__('filament.categories'))
                    ->relationship('categories', 'arabic_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('countries')->label(__('filament.countries'))
                    ->relationship('countries', 'arabic_name')
                    ->searchable()
                    ->preload(),

            ], FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()->button()->color('zinc'),
                DeleteAction::make()->button()->color('danger'),
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
