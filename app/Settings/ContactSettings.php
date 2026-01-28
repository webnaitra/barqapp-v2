<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    public string $contact_title;
    public string $contact_introduction;
    public string $contact_form_title;
    public string $contact_form_introduction;
    public string $contact_address_title;
    public string $contact_address_content;
    public string $contact_email_title; // Was contact_email_content in user list, likely title/content pair
    public string $contact_email_content;
    public string $contact_phone_title;
    public string $contact_phone_content;

    public static function group(): string
    {
        return 'contact';
    }
}
