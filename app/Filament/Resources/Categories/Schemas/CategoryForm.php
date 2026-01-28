<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ColorPicker;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('arabic_name')
                    ->required(),
                ColorPicker::make('color')
                    ->default(null),
                FileUpload::make('image')
                    ->image()
                    ->default(null)
                    ->columnSpan(2),
                TextInput::make('icon_class')
                    ->default(null),
                TextInput::make('order')
                    ->numeric()
                    ->default(null),
                Toggle::make('freeze'),
                Toggle::make('featured')
                    ->required(),
            ]);
    }
}
