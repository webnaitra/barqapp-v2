<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AffiliateResource\Pages\ListAffiliates;
use App\Filament\Resources\AffiliateResource\Pages\CreateAffiliate;
use App\Filament\Resources\AffiliateResource\Pages\EditAffiliate;
use App\Filament\Resources\AffiliateResource\Pages;
use App\Filament\Resources\AffiliateResource\RelationManagers;
use App\Models\Affiliate;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;

class AffiliateResource extends Resource
{
    protected static ?string $model = Affiliate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rocket-launch';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.ads_management');
    }

        public static function getModelLabel(): string
    {
        return __('filament.affiliate');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.affiliates');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')->label(__('filament.name'))
                    ->maxLength(255)
                    ->default(null)
                    ->columnSpan(2),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
                    ->columnSpan(2),
                TextArea::make('description')->label(__('filament.description'))
                    ->maxLength(255)
                    ->default(null)
                    ->columnSpan(2),
                TextInput::make('url')->label(__('filament.url'))
                    ->maxLength(255)
                    ->url()
                    ->default(null)
                    ->columnSpan(2),
                TextInput::make('price')->label(__('filament.price'))
                    ->maxLength(255)
                    ->numeric()
                    ->default(null),
                TextInput::make('selling_price')->label(__('filament.selling_price'))
                    ->maxLength(191)
                    ->numeric() 
                    ->default(null),
                Select::make('country_id')->label(__('filament.country_id'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('filament.country')),
                Select::make('categories')->label(__('filament.categories'))
                    ->relationship('categories', 'name')
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
                TextColumn::make('country.name')->label(__('filament.countryname'))
                    ->sortable(),
                TextColumn::make('categories.name')->label(__('filament.categoriesname'))
                    ->sortable(),
                TextColumn::make('productCategories.name')->label(__('filament.productcategoriesname'))
                    ->sortable(),
                TextColumn::make('url')->label(__('filament.url'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')->label(__('filament.price'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('selling_price')->label(__('filament.selling_price'))
                    ->searchable()
                     ->toggleable(isToggledHiddenByDefault: true),

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
            SelectFilter::make('categories')->label(__('filament.categories'))
                ->relationship('categories', 'name')
                ->searchable()
                ->preload(),
            SelectFilter::make('productCategories')->label(__('filament.productcategories'))
                ->relationship('productCategories', 'name')
                ->searchable()
                ->preload(),
            SelectFilter::make('country')->label(__('filament.country'))
                ->relationship('country', 'name')
                ->searchable()
                ->preload(),
            ], layout: FiltersLayout::AboveContent)
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
            'index' => ListAffiliates::route('/'),
            'create' => CreateAffiliate::route('/create'),
            'edit' => EditAffiliate::route('/{record}/edit'),
        ];
    }
}
