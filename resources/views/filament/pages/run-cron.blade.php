<x-filament-panels::page>
    @if($startFetch)
        <div class="space-y-6">
            <div class="flex justify-between items-center">

            </div>

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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
        </div>
    @endif
</x-filament-panels::page>
