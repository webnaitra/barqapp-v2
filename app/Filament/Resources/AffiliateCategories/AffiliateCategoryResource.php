<?php

namespace App\Filament\Resources\AffiliateCategories;

use Filament\Schemas\Schema;
use App\Filament\Resources\AffiliateCategories\Pages\CreateAffiliateCategory;
use App\Filament\Resources\AffiliateCategories\Pages\EditAffiliateCategory;
use App\Filament\Resources\AffiliateCategories\Pages\ListAffiliateCategories;
use App\Filament\Resources\AffiliateCategories\Schemas\AffiliateCategoryForm;
use App\Filament\Resources\AffiliateCategories\Tables\AffiliateCategoriesTable;
use App\Models\ProductCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AffiliateCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-server-stack';
    protected static string | \UnitEnum | null $navigationGroup = 'Ads Management';
    protected static ?string $modelLabel = 'Affiliate Category';
    protected static ?string $pluralModelLabel = 'Affiliate Categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AffiliateCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AffiliateCategoriesTable::configure($table);
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
            'index' => ListAffiliateCategories::route('/'),
            'create' => CreateAffiliateCategory::route('/create'),
            'edit' => EditAffiliateCategory::route('/{record}/edit'),
        ];
    }
}
