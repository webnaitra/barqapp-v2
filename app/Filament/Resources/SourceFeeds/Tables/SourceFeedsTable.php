<?php

namespace App\Filament\Resources\SourceFeeds\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class SourceFeedsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label(__('filament.id'))
                    ->searchable(),
                TextColumn::make('source_url')->label(__('filament.source_url'))
                    ->searchable(),
                TextColumn::make('source.arabic_name')->label(__('filament.sourcearabic_name'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('category.arabic_name')->label(__('filament.categoryarabic_name'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('status_id')->label(__('filament.status_id'))->label(__('filament.valid_url'))
                    ->boolean(),
                ToggleColumn::make('freeze')->label(__('filament.freeze')),
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
            
                SelectFilter::make('source')->label(__('filament.source'))
                    ->relationship('source', 'arabic_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category')->label(__('filament.category'))
                    ->relationship('category', 'arabic_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status_id')->label(__('filament.status_id'))
                    ->label(__('filament.status'))
                    ->options([
                        1 => 'Valid',
                        0 => 'Invalid',
                    ]),
            ],  layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('edit')->label(__('filament.edit'))
                ->label(__('filament.test'))
                ->icon('heroicon-o-play')
                ->button()
                ->outlined()
                ->url(fn ( $record ) => url('#'))
                ->color('gray')
                ->openUrlInNewTab(),
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
