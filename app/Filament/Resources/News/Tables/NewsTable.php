<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament.name'))
                    ->searchable(),
                TextColumn::make('category.arabic_name')->label(__('filament.category'))
                    ->sortable(),
                TextColumn::make('sources.arabic_name')->label(__('filament.sources'))
                    ->sortable(),
                TextColumn::make('date')->label(__('filament.date'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('countries.arabic_name')->label(__('filament.countries'))
                    ->sortable()
                    ->label(__('filament.countries'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tags.tag_name')->label(__('filament.tags'))
                    ->sortable()
                    ->label(__('filament.tags'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('keywords.keyword_name')->label(__('filament.keyword_name'))
                    ->sortable()
                    ->label(__('filament.keywords'))
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('urgent')->label(__('filament.urgent'))
                    ->sortable()
                    ->label(__('filament.trending'))
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source_link')->label(__('filament.source_link'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('run_cron')->label(__('filament.run_cron'))
                    ->boolean()
                    ->label(__('filament.processing_pending'))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_updated')->label(__('filament.is_updated'))
                    ->boolean()
                    ->label(__('filament.processing_complete'))
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
                SelectFilter::make('tags')->label(__('filament.tags'))
                ->relationship('tags', 'tag_name')
                ->searchable()
                ->preload(),
                SelectFilter::make('keywords')->label(__('filament.keywords'))
                ->relationship('keywords', 'keyword_name')
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
            ->filtersFormColumns(4)
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
