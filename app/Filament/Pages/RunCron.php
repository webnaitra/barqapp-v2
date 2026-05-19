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
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessFeedJob;
use App\Models\ArticleFetchLog;
use App\Models\SourceFeed;

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
    public ?array $data = [];
    public $totalFeeds = 0;
    public $batchId = null;
    public $isProcessing = false;
    public $isFetched = false;
    public $startedAt = null;

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
        return $table
            ->query(
                $this->batchId 
                    ? ArticleFetchLog::where('batch_id', $this->batchId)->latest() 
                    : ArticleFetchLog::query()->where('id', -1) // Empty query if no batch
            )
            ->poll('5s')
            ->columns([
                TextColumn::make('source_name')
                    ->label(__('filament.source'))
                    ->toggleable(),
                TextColumn::make('title')
                    ->label(__('filament.title'))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('filament.date'))
                    ->dateTime(),
                TextColumn::make('status')
                    ->label(__('filament.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'New' => 'success',
                        'Already exist' => 'warning',
                        'Error' => 'danger',
                        default => 'secondary',
                    }),
                ]);
    }


    public function submit()
    {
        $this->startFetch = true;
        $this->isProcessing = true;
        $this->isFetched = false;
        $this->startedAt = now();

        $query = SourceFeed::whereHas('source', function($q) {
            $q->select('name','source_url','category_id','source_id');
        });

        if(!empty($this->data['categoryId'])){
            $query->where('category_id', $this->data['categoryId']);
        }
        if(!empty($this->data['sourceId'])){
            $query->where('source_id', $this->data['sourceId']);
        }

        $feeds = $query->get();
        $this->totalFeeds = $feeds->count();

        if ($this->totalFeeds > 0) {
            $jobs = [];
            foreach ($feeds as $feed) {
                $jobs[] = new ProcessFeedJob($feed, false);
            }

            $batch = Bus::batch($jobs)
                ->name('Fetch Articles')
                ->onQueue('fetch-articles')
                ->dispatch();

            $this->batchId = $batch->id;
        } else {
            $this->isProcessing = false;
            $this->isFetched = true;
            Notification::make()
                ->title('No feeds found')
                ->warning()
                ->send();
        }
    }

    public function checkBatchStatus()
    {
        if ($this->batchId && $this->isProcessing) {
            $batch = Bus::findBatch($this->batchId);
            if ($batch && $batch->finished()) {
                $this->isProcessing = false;
                $this->isFetched = true;
                Notification::make()
                    ->title('Fetch Completed')
                    ->success()
                    ->send();
            }
        }
    }

    public function getBatchProperty()
    {
        return $this->batchId ? Bus::findBatch($this->batchId) : null;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
