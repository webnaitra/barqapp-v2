<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.app_logo', '');
        $this->migrator->add('general.footer_logo', '');
        $this->migrator->add('general.footer_text', '');
        $this->migrator->add('general.app_header', '');
        $this->migrator->add('general.app_download_text', '');
        $this->migrator->add('general.copyright', '');
        $this->migrator->add('general.newsletter_text', '');
        $this->migrator->add('general.app_google_play', '');
        $this->migrator->add('general.app_app_store', '');
        $this->migrator->add('general.app_facebook', '');
        $this->migrator->add('general.app_twitter', '');
        $this->migrator->add('general.app_whatsapp', '');
        $this->migrator->add('general.app_massenger', '');
        $this->migrator->add('general.app_instagram', '');
        $this->migrator->add('general.app_youtube', '');
        $this->migrator->add('general.app_tiktok', '');
        $this->migrator->add('general.contact_title', '');
        $this->migrator->add('general.contact_introduction', '');
        $this->migrator->add('general.contact_form_title', '');
        $this->migrator->add('general.contact_form_introduction', '');
        $this->migrator->add('general.contact_address_title', '');
        $this->migrator->add('general.contact_address_content', '');
        $this->migrator->add('general.contact_email_title', '');
        $this->migrator->add('general.contact_email_content', '');
        $this->migrator->add('general.contact_phone_title', '');
        $this->migrator->add('general.contact_phone_content', '');
        $this->migrator->add('general.banner_1', '');
        $this->migrator->add('general.banner_2', '');
        $this->migrator->add('general.banner_3', '');
        $this->migrator->add('general.banner_1_link', '');
        $this->migrator->add('general.banner_2_link', '');
        $this->migrator->add('general.banner_3_link', '');
    }   
};
