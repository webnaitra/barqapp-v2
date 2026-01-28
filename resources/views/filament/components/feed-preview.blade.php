@if($error)
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">Error!</span> {{ $error }}
    </div>
@else
    <div class="relative overflow-x-auto max-h-[500px] overflow-y-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0 z-10 shadow-sm">
                <tr>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-700">
                        {{ __('filament.title') }}
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-700">
                        {{ __('filament.date') }}
                    </th>
                    <th scope="col" class="px-6 py-3 bg-gray-50 dark:bg-gray-700">
                        {{ __('filament.link') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @if(isset($items) && count($items) > 0)
                    @foreach($items as $item)
                        <tr class="border-b dark:border-gray-700 {{ $loop->even ? 'bg-gray-100 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }}">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item['title'] ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ \Carbon\Carbon::parse($item['date'])->format('d M, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ $item['link'] ?? '#' }}" target="_blank" class="inline-block px-4 py-2 text-white bg-red-500 rounded hover:bg-red-600 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900">
                                    {{ __('filament.open') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <td colspan="3" class="px-6 py-4 text-center">
                            {{ __('filament.no_items_found') }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endif
