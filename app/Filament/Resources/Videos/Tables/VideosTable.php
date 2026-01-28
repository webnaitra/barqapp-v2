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
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('sources.arabic_name')
                    ->searchable()
                    ->label('Source'),
                TextColumn::make('category.name')
                    ->sortable()
                    ->label('Category'),
                TextColumn::make('countries.arabic_name')
                    ->sortable()
                    ->label('Country'),
                TextColumn::make('video')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source_link')
                    ->searchable()
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
                ->relationship('category', 'name')
                ->searchable()
                ->preload(),
                SelectFilter::make('countries')
                ->relationship('countries', 'arabic_name')
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
            ->filtersFormColumns(5)
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
