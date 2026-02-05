<?php

namespace App\Filament\Resources\Keywords\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class KeywordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('keyword_name')->label(__('filament.keyword_name'))
                    ->searchable(),
                TextColumn::make('category.arabic_name')->label(__('filament.category'))
                    ->label(__('filament.category'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('countries.arabic_name')->label(__('filament.countries'))
                    ->label(__('filament.countries'))
                    ->searchable(),
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
                    ->relationship('countries', 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('category')->label(__('filament.category'))
                    ->relationship('category', 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ], FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make()->button()->color('zinc'),
                DeleteAction::make()->button()->color('danger'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
