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

    protected static string | \UnitEnum | null $navigationGroup = 'Ads Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('name')
                    ->maxLength(255)
                    ->default(null)
                    ->columnSpan(2),
                FileUpload::make('image')
                    ->image()
                    ->columnSpan(2),
                TextArea::make('description')
                    ->maxLength(255)
                    ->default(null)
                    ->columnSpan(2),
                TextInput::make('url')
                    ->maxLength(255)
                    ->url()
                    ->default(null)
                    ->columnSpan(2),
                TextInput::make('price')
                    ->maxLength(255)
                    ->numeric()
                    ->default(null),
                TextInput::make('selling_price')
                    ->maxLength(191)
                    ->numeric() 
                    ->default(null),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Country'),
                Select::make('categories')
                    ->relationship('categories', 'name')
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
                TextColumn::make('country.name')
                    ->sortable(),
                TextColumn::make('categories.name')
                    ->sortable(),
                TextColumn::make('productCategories.name')
                    ->sortable(),
                TextColumn::make('url')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('selling_price')
                    ->searchable()
                     ->toggleable(isToggledHiddenByDefault: true),

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
            SelectFilter::make('categories')
                ->relationship('categories', 'name')
                ->searchable()
                ->preload(),
            SelectFilter::make('productCategories')
                ->relationship('productCategories', 'name')
                ->searchable()
                ->preload(),
            SelectFilter::make('country')
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
