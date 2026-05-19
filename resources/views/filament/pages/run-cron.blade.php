<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                Submit
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="submit" />
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8" style="margin-top:20px; margin-bottom:20px;">
        @if($startFetch)
            @php
                $batch = $this->batch;
                $progress = $batch ? $batch->progress() : 0;
                $processed = $batch ? $batch->processedJobs() : 0;
                $totalFeeds = $batch ? $batch->totalJobs : $totalFeeds;
            @endphp
            <div wire:poll.5s="checkBatchStatus" class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center gap-2">
                        @if($isProcessing)
                            <x-filament::loading-indicator class="h-5 w-5 text-primary-600" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Crawling feeds...</span>
                        @else
                            <x-heroicon-o-check-circle class="h-5 w-5 text-success-600" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Crawling completed</span>
                        @endif
                    </div>
                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/30 px-2 py-1 rounded-full">
                        {{ round($progress) }}%
                    </span>
                </div>
                
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-500 ease-out" 
                         style="width: {{ $progress }}%">
                    </div>
                </div>
                
                <div class="flex justify-between mt-2 text-[10px] text-gray-500 uppercase tracking-wider font-semibold">
                    <span>Processed {{ $processed }} / {{ $totalFeeds }} Feeds</span>
                    <span>Started: {{ $startedAt ? \Carbon\Carbon::parse($startedAt)->format('H:i:s') : 'N/A' }}</span>
                </div>
            </div>

            {{ $this->table }}
        @else
            <div class="p-12 text-center text-gray-500 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-gray-900/50">
                <x-heroicon-o-cloud-arrow-down class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-4"/>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Ready to Fetch</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Select filters and click submit to start crawling articles.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>