<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;

class ManageSettings extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                FileUpload::make('app_logo')->label('App Logo')->image()->columnSpanFull(),
                                FileUpload::make('footer_logo')->label('Footer Logo')->image()->columnSpanFull(),
                                Textarea::make('footer_text')->label('Footer Text')->columnSpanFull(),
                                TextInput::make('app_header')->label('App Header')->columnSpanFull(),
                                Textarea::make('app_download_text')->label('Download Text')->columnSpanFull(),
                                TextInput::make('copyright')->label('Copyright')->columnSpanFull(),
                                Textarea::make('newsletter_text')->label('Newsletter Text')->columnSpanFull(),
                                TextInput::make('app_google_play')->label('Google Play URL')->columnSpanFull(),
                                TextInput::make('app_app_store')->label('App Store URL')->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedArchiveBox),
                        
                        Tab::make('Social Media')
                            ->schema([
                                TextInput::make('app_facebook')->label('Facebook')->url()->columnSpanFull(),
                                TextInput::make('app_twitter')->label('Twitter')->url()->columnSpanFull(),
                                TextInput::make('app_whatsapp')->label('WhatsApp')->columnSpanFull(),
                                TextInput::make('app_massenger')->label('Messenger')->columnSpanFull(),
                                TextInput::make('app_instagram')->label('Instagram')->url()->columnSpanFull(),
                                TextInput::make('app_youtube')->label('YouTube')->url()->columnSpanFull(),
                                TextInput::make('app_tiktok')->label('TikTok')->url()->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedGlobeEuropeAfrica),

                        Tab::make('Contact Info')
                            ->schema([
                                TextInput::make('contact_title')->label('Page Title')->columnSpanFull(),
                                Textarea::make('contact_introduction')->label('Introduction')->columnSpanFull(),
                                TextInput::make('contact_form_title')->label('Form Title')->columnSpanFull(),
                                Textarea::make('contact_form_introduction')->label('Form Intro')->columnSpanFull(),
                                TextInput::make('contact_address_title')->label('Address Title')->columnSpanFull(),
                                Textarea::make('contact_address_content')->label('Address')->columnSpanFull(),
                                TextInput::make('contact_email_title')->label('Email Title')->columnSpanFull(),
                                TextInput::make('contact_email_content')->label('Email')->columnSpanFull(),
                                TextInput::make('contact_phone_title')->label('Phone Title')->columnSpanFull(),
                                TextInput::make('contact_phone_content')->label('Phone')->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedEnvelope),

                        Tab::make('Ads & Banners')
                            ->schema([
                                FileUpload::make('banner_1')->label('Banner 1')->image()->columnSpanFull(),
                                TextInput::make('banner_1_link')->label('Banner 1 Link')->url()->columnSpanFull(),
                                FileUpload::make('banner_2')->label('Banner 2')->image()->columnSpanFull(),
                                TextInput::make('banner_2_link')->label('Banner 2 Link')->url()->columnSpanFull(),
                                FileUpload::make('banner_3')->label('Banner 3')->image()->columnSpanFull(),
                                TextInput::make('banner_3_link')->label('Banner 3 Link')->url()->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedTv),
                    ])->columnSpan('full')
            ]);
    }
}
