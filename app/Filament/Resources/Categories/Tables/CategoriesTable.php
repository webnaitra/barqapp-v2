<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Pages\RunCron;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament.name'))
                    ->searchable(),
                TextColumn::make('slug')->label(__('filament.slug'))
                    ->searchable(),
                TextColumn::make('arabic_name')->label(__('filament.arabic_name'))
                    ->searchable(),
                ToggleColumn::make('freeze')->label(__('filament.freeze')),
                ToggleColumn::make('featured')->label(__('filament.featured')),
                TextColumn::make('news_count')
                ->counts('news')
                ->badge()
                ->label(__('filament.news_count'))
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
            ->reorderable('order')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('emptyArticles')
                    ->label(__('filament.empty'))
                    ->color('danger')
                    ->icon('heroicon-s-folder-open')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        Artisan::call('cron:empty-articles', ['categoryId' => $record->id]);
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
                        'run_cron_category_id' => $record->id,
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
