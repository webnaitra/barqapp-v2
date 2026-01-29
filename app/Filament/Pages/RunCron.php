<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use App\Models\Category;
use App\Models\Source;

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
            Action::make('fetchAllArticles')
                ->label(__('filament.fetch_all_articles'))
                ->color('primary')
                ->form([
                    Select::make('categoryId')
                        ->label(__('filament.category'))
                        ->options(Category::pluck('arabic_name', 'id'))
                        ->placeholder('All Categories')
                        ->searchable(),
                    Select::make('sourceId')
                        ->label(__('filament.source'))
                        ->options(Source::pluck('arabic_name', 'id'))
                        ->placeholder('All Sources')
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    return redirect(RunCron::getUrl())->with([
                        'run_cron_action' => 'fetch',
                        'run_cron_category_id' => $data['categoryId'],
                        'run_cron_source_id' => $data['sourceId'],
                    ]);
                }),
        ];
    }
}
