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
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('arabic_name')
                    ->required(),
                ColorPicker::make('color')
                    ->default(null),
                FileUpload::make('image')
                    ->image()
                    ->default(null),
                TextInput::make('icon_class')
                    ->default(null),
                TextInput::make('order')
                    ->numeric()
                    ->default(null),
                Toggle::make('freeze')
                    ->required(),
                Toggle::make('featured')
                    ->required(),
            ]);
    }
}
