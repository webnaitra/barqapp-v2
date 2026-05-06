<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end" style="margin-top:20px; margin-bottom:20px; display:flex; justify-content:end; align-items:center;">
            <x-filament::button type="submit" wire:loading.attr="disabled">
                Submit
                <x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="submit" />
            </x-filament::button>
        </div>
    </form>

    <div class="mt-8">
        @if($isFetched)
            {{ $this->table }}
        @else
            <div class="p-6 text-center text-gray-500 border border-dashed rounded-xl">
                Select filters and click submit to fetch articles.
            </div>
        @endif
    </div>
</x-filament-panels::page>