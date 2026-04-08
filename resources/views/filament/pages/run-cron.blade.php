<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
        <div style="margin-top:20px; margin-bottom:20px; display:flex; justify-content:end; align-items:center;">
            <x-filament::button color="danger"  type="submit">
                Submit
            </x-filament::button>
        </div>
    </form>

    <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

    @if($startFetch)
        <div class="space-y-6">
            @if($isProcessing || $offset > 0)
                <div class="flex items-center gap-3 p-4 bg-primary-50 dark:bg-primary-900/10 border border-primary-200 dark:border-primary-800 rounded-xl">
                    <x-filament::loading-indicator class="h-5 w-5 text-primary-600" wire:loading wire:target="runFetch" />
                    <span class="text-sm font-medium text-primary-700 dark:text-primary-400">
                        {{ $isProcessing ? 'Processing' : 'Completed' }}: {{ min($offset, $totalFeeds) }} / {{ $totalFeeds }} Feeds
                    </span>
                </div>
            @endif

            @foreach($fetchResults as $feedResult)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <h3 class="font-medium text-gray-900 dark:text-gray-100">
                            Feed: {{ $feedResult['feed_url'] }}
                        </h3>
                        @if($feedResult['status'] == 'error')
                             <span class="text-sm text-red-600">{{ $feedResult['message'] }}</span>
                        @endif
                    </div>
                    
                    @if(!empty($feedResult['items']))
                    <div class="relative overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">#</th>
                                    <th scope="col" class="px-6 py-3">{{ __('filament.title') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ __('filament.date') }}</th>
                                    <th scope="col" class="px-6 py-3">{{ __('filament.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feedResult['items'] as $index => $item)
                                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ \Illuminate\Support\Str::limit($item['title'], 50) }}
                                        </td>
                                        <td class="px-6 py-4">{{ $item['date'] }}</td>
                                        <td class="px-6 py-4">
                                            <span class="@if($item['status'] == 'New') text-green-600 @elseif($item['status'] == 'Already exist') text-yellow-600 @else text-red-600 @endif font-medium">
                                                {{ $item['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <div class="p-6 text-center text-gray-500">{{__('filament.no_items_found')}}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6 text-center text-gray-500">
           Select filters and click submit to fetch articles.
        </div>
    @endif
</x-filament-panels::page>
