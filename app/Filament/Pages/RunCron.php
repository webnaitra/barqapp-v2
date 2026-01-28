<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class RunCron extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-forward';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected string $view = 'filament.pages.run-cron';
}
