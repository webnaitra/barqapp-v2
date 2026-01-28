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
                TextInput::make('name')->label(__('filament.name'))
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->label(__('filament.slug')),
                Repeater::make('menuItems')->label(__('filament.menuitems'))
                ->relationship()
                ->schema([
                    TextInput::make('name')->label(__('filament.name'))->required(),
                    Select::make('type')->label(__('filament.type'))
                    ->options([
                        'internal' => 'Internal',
                        'external' => 'External',
                    ])
                    ->default('internal')
                    ->required(),
                    TextInput::make('url')->label(__('filament.url')),
                    Select::make('target')->label(__('filament.target'))
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
                ->label(__('filament.menu_items')),
            ]);
    }
}
