<?php

namespace App\Filament\Resources\AffiliateCategories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;

class AffiliateCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->required(),
                TextInput::make('slug')->label(__('filament.slug'))
                    ->required(),
                TextInput::make('arabic_name')->label(__('filament.arabic_name'))
                    ->required(),
                ColorPicker::make('color')->label(__('filament.color'))
                    ->default(null),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->default(null),
                TextInput::make('icon_class')->label(__('filament.icon_class'))
                    ->default(null),
                TextInput::make('order')->label(__('filament.order'))
                    ->numeric()
                    ->default(null),
                Toggle::make('freeze')->label(__('filament.freeze'))
                    ->required(),
                Toggle::make('featured')->label(__('filament.featured'))
                    ->required(),
            ]);
    }
}
