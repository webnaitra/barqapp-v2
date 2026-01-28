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
    
    public $startFetch = false;
    public $fetchResults = [];
    public $filters = ['categoryId' => null, 'sourceId' => null];

    protected $listeners = ['fetchArticles' => 'runFetch'];

    public function mount()
    {
        if (session()->has('run_cron_action') && session('run_cron_action') == 'fetch') {
             $this->filters['categoryId'] = session('run_cron_category_id');
             $this->filters['sourceId'] = session('run_cron_source_id');
             $this->runFetch();
        }
    }

    public function runFetch() 
    {
        $service = new \App\Services\FetchArticlesService();
        $this->fetchResults = $service->fetch(
            $this->filters['categoryId'], 
            $this->filters['sourceId']
        );
        $this->startFetch = true;
    }


    protected function getHeaderActions(): array
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
                        ->placeholder('All Categories')
                        ->searchable(),
                    \Filament\Forms\Components\Select::make('sourceId')
                        ->label(__('filament.source'))
                        ->options(\App\Models\Source::pluck('name', 'id'))
                        ->placeholder('All Sources')
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
                        ->placeholder('All Categories')
                        ->searchable(),
                    \Filament\Forms\Components\Select::make('sourceId')
                        ->label(__('filament.source'))
                        ->options(\App\Models\Source::pluck('name', 'id'))
                        ->placeholder('All Sources')
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    $this->filters['categoryId'] = $data['categoryId'];
                    $this->filters['sourceId'] = $data['sourceId'];
                    $this->runFetch();
                }),
        ];
    }
}
