<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug'),
                Repeater::make('menuItems')
                ->relationship()
                ->schema([
                    TextInput::make('name')->required(),
                    Select::make('type')
                    ->options([
                        'internal' => 'Internal',
                        'external' => 'External',
                    ])
                    ->default('internal')
                    ->required(),
                    TextInput::make('url'),
                    Select::make('target')
                    ->options([
                        '_self' => 'Self',
                        '_blank' => 'Blank',
                        '_parent' => 'Parent',
                    ])
                    ->default('_self')
                    ->required(),
                ])
                ->orderColumn('order')
                ->columns(4)
                ->columnSpan('full')
                ->label('Menu Items'),
            ]);
    }
}
