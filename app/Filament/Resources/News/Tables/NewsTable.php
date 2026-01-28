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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category.arabic_name')
                    ->sortable(),
                TextColumn::make('sources.arabic_name')
                    ->sortable(),
                TextColumn::make('date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tags.tag_name')
                    ->sortable()
                    ->label('Tags')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('keywords.keyword_name')
                    ->sortable()
                    ->label('Keywords')
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('urgent')
                    ->sortable()
                    ->label('Trending')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source_link')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('run_cron')
                    ->boolean()
                    ->label('Processing Pending')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_updated')
                    ->boolean()
                    ->label('Processing Complete')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('sources')
                ->relationship('sources', 'arabic_name')
                ->searchable()
                ->preload(),
                SelectFilter::make('category')
                ->relationship('category', 'arabic_name')
                ->searchable()
                ->preload(),
                SelectFilter::make('tags')
                ->relationship('tags', 'tag_name')
                ->searchable()
                ->preload(),
                SelectFilter::make('keywords')
                ->relationship('keywords', 'keyword_name')
                ->searchable()
                ->preload(),
                Filter::make('created_from')
                    ->schema([
                        DatePicker::make('created_from'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            );
                           
                    }),
                    Filter::make('created_until')
                    ->schema([
                        DatePicker::make('created_until'),
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
