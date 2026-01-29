<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->default(null)->required(),
                TextInput::make('arabic_name')->label(__('filament.arabic_name'))
                    ->default(null)->required(),
                TextInput::make('currency_code')->label(__('filament.currency_code'))
                    ->default(null)->required(),
                TextInput::make('arabic_currency_name')->label(__('filament.arabic_currency_name'))
                    ->default(null)->required(),
                TextInput::make('country_code')->label(__('filament.country_code'))
                    ->default(null)->required(),
            ]);
    }
}
