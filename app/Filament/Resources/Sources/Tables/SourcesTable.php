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
use App\Filament\Pages\RunCron;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

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
                TextColumn::make('countries.arabic_name')->label(__('filament.countries'))
                    ->label(__('filament.countries'))
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
                SelectFilter::make('countries')->label(__('filament.countries'))
                    ->relationship('countries', 'arabic_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ],layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make('emptyArticles')
                    ->label(__('filament.empty'))
                    ->icon('heroicon-s-folder-open')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        Artisan::call('cron:empty-articles', ['sourceId' => $record->id, 'olderThanDays' => 3]);
                        Notification::make()
                            ->title('Articles Emptied')
                            ->success()
                            ->send();
                    })
                    ->button()
                    ->outlined()
                    ->color('zinc'),
                Action::make('fetchArticles')
                    ->label(__('filament.fetch'))
                    ->icon('heroicon-s-folder-arrow-down')
                    ->action(fn ($record) => redirect(RunCron::getUrl())->with([
                        'run_cron_action' => 'fetch',
                        'run_cron_source_id' => $record->id,
                    ]))
                    ->button()
                    ->outlined()
                    ->color('zinc'),
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
