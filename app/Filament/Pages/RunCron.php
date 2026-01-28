<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class RunCron extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-forward';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.run_cron');
    }

    public function getTitle(): string
    {
        return __('filament.run_cron');
    }
    protected string $view = 'filament.pages.run-cron';

    protected function getActions(): array
    {
        return [
            \Filament\Actions\Action::make('emptyArticles')
                ->label(__('filament.empty_articles'))
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\TextInput::make('olderThanDays')
                        ->label(__('filament.older_than_days'))
                        ->numeric()
                        ->default(3)
                        ->helperText(__('filament.older_than_days_helper')),
                    \Filament\Forms\Components\Select::make('categoryId')
                        ->label(__('filament.category'))
                        ->options(\App\Models\Category::pluck('name', 'id'))
                        ->searchable(),
                    \Filament\Forms\Components\Select::make('sourceId')
                        ->label(__('filament.source'))
                        ->options(\App\Models\Source::pluck('name', 'id'))
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    \Illuminate\Support\Facades\Artisan::call('cron:empty-articles', [
                        'olderThanDays' => $data['olderThanDays'],
                        'categoryId' => $data['categoryId'],
                        'sourceId' => $data['sourceId'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title(__('filament.success'))
                        ->body(\Illuminate\Support\Facades\Artisan::output())
                        ->success()
                        ->send();
                }),

            \Filament\Actions\Action::make('fetchAllArticles')
                ->label(__('filament.fetch_all_articles'))
                ->color('primary')
                ->form([
                    \Filament\Forms\Components\Select::make('categoryId')
                        ->label(__('filament.category'))
                        ->options(\App\Models\Category::pluck('name', 'id'))
                        ->searchable(),
                    \Filament\Forms\Components\Select::make('sourceId')
                        ->label(__('filament.source'))
                        ->options(\App\Models\Source::pluck('name', 'id'))
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    \Illuminate\Support\Facades\Artisan::call('cron:fetch-all-articles', [
                        'categoryId' => $data['categoryId'],
                        'sourceId' => $data['sourceId'],
                        'showOutput' => 1,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title(__('filament.success'))
                        ->body(\Illuminate\Support\Facades\Artisan::output())
                        ->success()
                        ->send();
                }),

            \Filament\Actions\Action::make('fetchFullArticles')
                ->label(__('filament.fetch_full_articles'))
                ->color('success')
                ->form([
                    \Filament\Forms\Components\TextInput::make('newsId')
                        ->label(__('filament.news_id'))
                        ->required(),
                ])
                ->action(function (array $data) {
                    \Illuminate\Support\Facades\Artisan::call('cron:fetch-articles', [
                        'news' => $data['newsId'],
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title(__('filament.success'))
                        ->body(\Illuminate\Support\Facades\Artisan::output())
                        ->success()
                        ->send();
                }),
        ];
    }
}
