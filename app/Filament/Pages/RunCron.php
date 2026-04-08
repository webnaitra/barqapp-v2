<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms;


use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use App\Models\Category;
use App\Models\Source;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Section;

class RunCron extends Page implements HasForms
{
    use InteractsWithForms;

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
    public ?array $data = [];
    public $totalFeeds = 0;
    public $offset = 0;
    public $batchSize = 10;
    public $isProcessing = false;

    protected $listeners = ['fetchArticles' => 'runFetch', 'nextBatch' => 'runFetch'];

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
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
                    ])->columns(2)
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $this->offset = 0;
        $this->fetchResults = [];
        $this->totalFeeds = 0;
        $this->startFetch = true;
        $this->runFetch();
    }

    public function runFetch() 
    {
        $this->isProcessing = true;
        $service = new \App\Services\FetchArticlesService();
        
        if ($this->offset === 0) {
            $this->totalFeeds = $service->countFeeds(
                $this->data['categoryId'] ?? null, 
                $this->data['sourceId'] ?? null
            );
        }

        $newResults = $service->fetch(
            $this->data['categoryId'] ?? null, 
            $this->data['sourceId'] ?? null,
            null,
            false,
            $this->batchSize,
            $this->offset
        );

        $this->fetchResults = array_merge($this->fetchResults, $newResults);
        $this->offset += $this->batchSize;

        if ($this->offset < $this->totalFeeds) {
            $this->dispatch('nextBatch');
        } else {
            $this->isProcessing = false;
            Notification::make()
                ->title('Fetch Completed')
                ->success()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
