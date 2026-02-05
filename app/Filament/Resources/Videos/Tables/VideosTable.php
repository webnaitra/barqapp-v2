<?php

namespace App\Filament\Resources\Videos\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class VideosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament.name'))
                    ->searchable(),
                TextColumn::make('sources.arabic_name')->label(__('filament.sourcesarabic_name'))
                    ->searchable()
                    ->label(__('filament.source')),
                TextColumn::make('category.arabic_name')->label(__('filament.categoryname'))
                    ->sortable()
                    ->label(__('filament.category')),
                TextColumn::make('countries.arabic_name')->label(__('filament.countriesarabic_name'))
                    ->label(__('filament.country')),
                TextColumn::make('video')->label(__('filament.video'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source_link')->label(__('filament.source_link'))
                    ->searchable()
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
                SelectFilter::make('sources')->label(__('filament.sources'))
                ->relationship('sources', 'arabic_name')
                ->searchable()
                ->preload(),
                SelectFilter::make('category')->label(__('filament.category'))
                ->relationship('category', 'arabic_name')
                ->searchable()
                ->preload(),
                SelectFilter::make('countries')->label(__('filament.countries'))
                ->relationship('countries', 'arabic_name')
                ->searchable()
                ->preload(),
                Filter::make('created_from')->label(__('filament.created_from'))
                    ->schema([
                        DatePicker::make('created_from')->label(__('filament.created_from')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            );
                           
                    }),
                    Filter::make('created_until')->label(__('filament.created_until'))
                    ->schema([
                        DatePicker::make('created_until')->label(__('filament.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(5)
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
