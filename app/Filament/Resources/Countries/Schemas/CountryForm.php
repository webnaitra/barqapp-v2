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
                TextInput::make('arabic_name')
                    ->default(null),
                TextInput::make('name')
                    ->default(null),
                TextInput::make('currency_code')
                    ->default(null),
                TextInput::make('arabic_currency_name')
                    ->default(null),
                TextInput::make('country_code')
                    ->default(null),
            ]);
    }
}
