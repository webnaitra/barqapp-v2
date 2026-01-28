<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SocialSettings extends Settings
{
    public string $app_facebook;
    public string $app_twitter;
    public string $app_whatsapp;
    public string $app_massenger;
    public string $app_instagram;
    public string $app_youtube;
    public string $app_tiktok;

    public static function group(): string
    {
        return 'social';
    }
}
