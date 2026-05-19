<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ColorPicker;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->label(__('filament.name'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->label(__('filament.slug'))
                    ->required(),
                TextInput::make('arabic_name')->label(__('filament.arabic_name'))
                    ->required(),
                ColorPicker::make('color')->label(__('filament.color'))
                    ->default(null),
                FileUpload::make('image')->label(__('filament.image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public')
                    ->default(null)
                    ->columnSpan(2),
                TextInput::make('icon_class')->label(__('filament.icon_class'))
                    ->default(null),
                TextInput::make('order')->label(__('filament.order'))
                    ->numeric()
                    ->default(null),
                Toggle::make('freeze')->label(__('filament.freeze')),
                Toggle::make('featured')->label(__('filament.featured'))
                    ->required(),
                Select::make('fetch_frequency')->label(__('Fetch Frequency'))
                    ->options([
                        '30' => 'Every 30 minutes',
                        '60' => 'Every 1 hour',
                        '120' => 'Every 2 hours',
                        '360' => 'Every 6 hours',
                        '480' => 'Every 8 hours (3 times a day)',
                        '720' => 'Every 12 hours (2 times a day)',
                        '1440' => 'Every 24 hours (1 time a day)',
                    ])
                    ->default(null)
                    ->placeholder('Default (Every 30 mins)'),
                TextInput::make('auto_expire_duration')->label(__('Auto Expire Duration (Days)'))
                    ->numeric()
                    ->default(null)
                    ->placeholder('Global Default (3 days)'),
            ]);
    }
}
