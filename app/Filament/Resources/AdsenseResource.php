<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\AdsenseResource\Pages\ListAdsenses;
use App\Filament\Resources\AdsenseResource\Pages\CreateAdsense;
use App\Filament\Resources\AdsenseResource\Pages\EditAdsense;
use App\Filament\Resources\AdsenseResource\Pages;
use App\Filament\Resources\AdsenseResource\RelationManagers;
use App\Models\Adsense;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class AdsenseResource extends Resource
{
    protected static ?string $model = Adsense::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-currency-dollar';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.ads_management');
    }

    public static function getModelLabel(): string
    {
        return __('filament.adsense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.adsenses');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('adsense_name')->label(__('filament.adsense_name'))
                    ->maxLength(255)
                    ->default(null)
                    ->label(__('filament.name')),
                CodeEditor::make('adsense_code')->label(__('filament.adsense_code'))
                    ->label(__('filament.code'))
                    ->columnSpanFull(),
                Select::make('category_id')->label(__('filament.category_id'))
                    ->relationship(name: 'category', titleAttribute: 'name')
                    ->required(),
                Select::make('location_id')->label(__('filament.location_id'))
                    ->relationship(name: 'location', titleAttribute: 'name'),
                Toggle::make('is_mobile')->label(__('filament.is_mobile'))
                    ->label(__('filament.for_mobile'))
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('adsense_name')->label(__('filament.adsense_name'))
                    ->searchable(),
                TextColumn::make('adsense_area')->label(__('filament.adsense_area'))
                    ->searchable(),
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
                //
            ])
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
            'index' => ListAdsenses::route('/'),
            'create' => CreateAdsense::route('/create'),
            'edit' => EditAdsense::route('/{record}/edit'),
        ];
    }
}
