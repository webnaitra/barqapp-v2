<?php

namespace App\Filament\Resources\Sources\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Repeater;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;

class SourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

Tabs::make('Tabs')
    ->tabs([
        Tab::make('General')
            ->icon(Heroicon::OutlinedFolder)
            ->schema([
                TextInput::make('name')
                    ->default(null),
                TextInput::make('arabic_name')
                    ->default(null),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Country'),
                Toggle::make('freeze')
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('website')
                    ->url()
                    ->default(null),
                FileUpload::make('placeholder_image')
                    ->image()
                    ->directory('public/files')
                    ->visibility('public'),
                FileUpload::make('logo')
                    ->image()
                    ->directory('public/files')
                    ->visibility('public'),
                Textarea::make('filter_classes')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('content_classes')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('image_classes')
                    ->default(null)
                    ->columnSpanFull(),
            ]),
        Tab::make('Source Feeds')
            ->icon(Heroicon::OutlinedRss)
            ->schema([
                Repeater::make('sourcefeeds')
                ->relationship()
                ->schema([
                    TextInput::make('source_url')->default(null)->required(),
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->label('Category'),
                    Toggle::make('freeze'),
                    Toggle::make('status_id')
                        ->label('Status'),
                ])
                ->extraItemActions([
                Action::make('testFeed')
                    ->icon(Heroicon::Play)
                    ->url(function (array $arguments, Repeater $component) {
                        $itemData = $component->getItemState($arguments['item']);
                        return $itemData['source_url'];
                    })
                    ->openUrlInNewTab()
                    ->label('Test Feed'),

                ])
                ->addActionLabel('Add Source Feed')
                ->columns(2)
            ])
    ])->columnSpan('full')
            ]);
    }
}
