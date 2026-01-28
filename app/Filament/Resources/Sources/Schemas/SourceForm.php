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

Tabs::make('Tabs')->label(__('filament.tabs'))
    ->tabs([
        Tab::make('General')->label(__('filament.general'))
            ->icon(Heroicon::OutlinedFolder)
            ->schema([
                TextInput::make('name')->label(__('filament.name'))
                    ->default(null),
                TextInput::make('arabic_name')->label(__('filament.arabic_name'))
                    ->default(null),
                Textarea::make('description')->label(__('filament.description'))
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('country_id')->label(__('filament.country_id'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('filament.country')),
                Toggle::make('freeze')->label(__('filament.freeze'))
                    ->required(),
                TextInput::make('phone')->label(__('filament.phone'))
                    ->tel()
                    ->default(null),
                TextInput::make('email')->label(__('filament.email'))
                    ->label(__('filament.email_address'))
                    ->email()
                    ->default(null),
                TextInput::make('website')->label(__('filament.website'))
                    ->url()
                    ->default(null),
                FileUpload::make('placeholder_image')->label(__('filament.placeholder_image'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public'),
                FileUpload::make('logo')->label(__('filament.logo'))
                    ->image()
                    ->directory('public/files')
                    ->visibility('public'),
                Textarea::make('filter_classes')->label(__('filament.filter_classes'))
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('content_classes')->label(__('filament.content_classes'))
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('image_classes')->label(__('filament.image_classes'))
                    ->default(null)
                    ->columnSpanFull(),
            ]),
        Tab::make('Source Feeds')->label(__('filament.source_feeds'))
            ->icon(Heroicon::OutlinedRss)
            ->schema([
                Repeater::make('sourcefeeds')->label(__('filament.sourcefeeds'))
                ->relationship()
                ->schema([
                    TextInput::make('source_url')->label(__('filament.source_url'))->default(null)->required(),
                    Select::make('category_id')->label(__('filament.category_id'))
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->label(__('filament.category')),
                    Toggle::make('freeze')->label(__('filament.freeze')),
                    Toggle::make('status_id')->label(__('filament.status_id'))
                        ->label(__('filament.status')),
                ])
                ->extraItemActions([
                Action::make('testFeed')->label(__('filament.testfeed'))
                    ->icon(Heroicon::Play)
                    ->url(function (array $arguments, Repeater $component) {
                        $itemData = $component->getItemState($arguments['item']);
                        return $itemData['source_url'];
                    })
                    ->openUrlInNewTab()
                    ->label(__('filament.test_feed')),

                ])
                ->addActionLabel('Add Source Feed')
                ->columns(2)
            ])
    ])->columnSpan('full')
            ]);
    }
}
