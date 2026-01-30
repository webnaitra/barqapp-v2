<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    // General
    public ?string $footer_text;
    public ?string $app_header;
    public ?string $app_download_text;
    public ?string $copyright;
    public ?string $newsletter_text;
    public ?string $app_google_play;
    public ?string $app_app_store;
    public ?string $app_source_filter_enabled;
    public ?string $app_category_filter_enabled;

    // Social
    public ?string $app_facebook;
    public ?string $app_twitter;
    public ?string $app_whatsapp;
    public ?string $app_massenger;
    public ?string $app_instagram;
    public ?string $app_youtube;
    public ?string $app_tiktok;

    // Contact
    public ?string $contact_title;
    public ?string $contact_introduction;
    public ?string $contact_form_title;
    public ?string $contact_form_introduction;
    public ?string $contact_address_title;
    public ?string $contact_address_content;
    public ?string $contact_email_title;
    public ?string $contact_email_content;
    public ?string $contact_phone_title;
    public ?string $contact_phone_content;

    // Ad
    public ?string $banner_1;
    public ?string $banner_2;
    public ?string $banner_3;
    public ?string $banner_1_link;
    public ?string $banner_2_link;
    public ?string $banner_3_link;

    public static function group(): string
    {
        return 'general';
    }
}
