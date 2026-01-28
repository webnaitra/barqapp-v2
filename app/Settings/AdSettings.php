<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class AdSettings extends Settings
{
    public string $banner_1;
    public string $banner_2;
    public string $banner_3;
    public string $banner_1_link;
    public string $banner_2_link;
    public string $banner_3_link;

    public static function group(): string
    {
        return 'ad';
    }
}
