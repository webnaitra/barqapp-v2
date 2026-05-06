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
use App\Models\News;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Pagination\LengthAwarePaginator;

class RunCron extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

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
    public $isFetched = false;

    protected $listeners = ['fetchArticles' => 'runFetch', 'nextBatch' => 'runFetch'];

    public function mount()
    {
        $data = [];

        $autoSubmit = false;
        if (session('run_cron_action') === 'fetch') {
            if ($categoryId = session('run_cron_category_id')) {
                $data['categoryId'] = $categoryId;
            }
            if ($sourceId = session('run_cron_source_id')) {
                $data['sourceId'] = $sourceId;
            }
            $autoSubmit = true;
        }

        $this->form->fill($data);

        if ($autoSubmit) {
            $this->submit();
        }
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

    public function table(Table $table): Table
    {
        $flattened = collect($this->fetchResults)
        ->pluck('items')
        ->collapse()
        ->values()
        ->all();

        return $table
            ->query(fn() => null) // No Eloquent query
            ->poll('5s') // Automatically refresh every 5 seconds to show new data
            ->records(function (int $page, int $recordsPerPage): LengthAwarePaginator  {
            // 1. Fetch and flatten your data
            $allItems = collect($this->fetchResults)
                ->pluck('items')
                ->collapse()
                ->values();
            $items = $allItems->forPage($page, $recordsPerPage);
                
            return new LengthAwarePaginator(
                $items,
                total: $allItems->count(),
                perPage: $recordsPerPage,
                currentPage: $page,
            );

            })
            ->columns([
                TextColumn::make('source_name')
                    ->label(__('filament.source'))
                    ->toggleable(),
                TextColumn::make('title')
                    ->label(__('filament.title'))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('date')
                    ->label(__('filament.date'))
                    ->dateTime(),
                TextColumn::make('status')
                    ->label(__('filament.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'New' => 'success',
                        'Already exist' => 'warning',
                        'New' => 'success',
                        'Existing' => 'warning',
                        default => 'danger',
                    }),
                ]);
    }


    public function submit()
    {
        $this->offset = 0;
        $this->fetchResults = [];
        $this->totalFeeds = 0;
        $this->startFetch = true;
        $this->isFetched = false;
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
            $this->isFetched = true;
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
