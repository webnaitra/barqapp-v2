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
                TextColumn::make('keyword_name')
                    ->searchable(),
                TextColumn::make('category.arabic_name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('countries.arabic_name')
                    ->label('Countries')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('country')
                    ->relationship('countries', 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('category')
                    ->relationship('category', 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ], FiltersLayout::AboveContent)
            ->recordActions([
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
