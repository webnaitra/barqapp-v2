<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\LiveStreams\LiveStreamResource;
use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\Sources\SourceResource;
use App\Filament\Resources\Videos\VideoResource;
use App\Filament\Resources\AdvertiserResource;
use App\Models\Advertiser;
use App\Models\Category;
use App\Models\LiveStream;
use App\Models\News;
use App\Models\Source;
use App\Models\Video;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('News', News::count())->url(NewsResource::getUrl()),
            Stat::make('Videos', Video::count())->url(VideoResource::getUrl()),
            Stat::make('Categories', Category::count())->url(CategoryResource::getUrl()),
            Stat::make('Source', Source::count())->url(SourceResource::getUrl()),
            Stat::make('Users', Advertiser::count())->url(AdvertiserResource::getUrl()),
            Stat::make('Livestream', LiveStream::count())->url(LiveStreamResource::getUrl()),
        ];
    }
}
