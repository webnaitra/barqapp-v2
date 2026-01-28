<?php

namespace App\Filament\Resources\Sources\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Actions\DeleteAction;

class SourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament.name'))
                    ->searchable(),
                TextColumn::make('arabic_name')->label(__('filament.arabic_name'))
                    ->searchable(),
                TextColumn::make('country.arabic_name')->label(__('filament.countryarabic_name'))
                    ->label(__('filament.country'))
                    ->sortable(),
                ToggleColumn::make('freeze')->label(__('filament.freeze')),
                TextColumn::make('phone')->label(__('filament.phone'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')->label(__('filament.email'))
                    ->label(__('filament.email_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('website')->label(__('filament.website'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('placeholder_image')->label(__('filament.placeholder_image'))
                ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('logo')->label(__('filament.logo'))
                ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label(__('filament.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label(__('filament.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('country')->label(__('filament.country'))
                    ->relationship('country', 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ],layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('emptyArticles')
                    ->label(__('filament.empty_articles'))
                    ->color('danger')
                    ->requiresConfirmation()
                    ->url(fn ($record) => route('source.empty', ['sourceId' => $record->id]))
                    ->openUrlInNewTab()
                    ->button()
                    ->outlined(),
                Action::make('fetchArticles')
                    ->label(__('filament.fetch_all_articles'))
                    ->url(fn ($record) => route('source.cron', ['sourceId' => $record->id]))
                    ->openUrlInNewTab()
                    ->button()
                    ->outlined(),
                EditAction::make()->button()->outlined(),
                DeleteAction::make()->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
