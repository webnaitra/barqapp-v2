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
    public static function getNavigationGroup(): ?string
    {
        return __('filament.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.settings');
    }

    public function getTitle(): string
    {
        return __('filament.settings');
    }

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('settings')
                    ->label(__('filament.settings'))
                    ->tabs([
                        Tab::make('general')
                            ->label(__('filament.general_settings'))
                            ->schema([
                                FileUpload::make('app_logo')->label(__('filament.app_logo'))->image()->directory('public/files')
                                ->visibility('public')->columnSpanFull(),
                                FileUpload::make('footer_logo')->label(__('filament.footer_logo'))->image()->directory('public/files')
                                ->visibility('public')->columnSpanFull(),
                                Textarea::make('footer_text')->label(__('filament.footer_text'))->columnSpanFull(),
                                TextInput::make('app_header')->label(__('filament.app_header'))->columnSpanFull(),
                                Textarea::make('app_download_text')->label(__('filament.download_text'))->columnSpanFull(),
                                TextInput::make('copyright')->label(__('filament.copyright'))->columnSpanFull(),
                                Textarea::make('newsletter_text')->label(__('filament.newsletter_text'))->columnSpanFull(),
                                TextInput::make('app_google_play')->label(__('filament.google_play_url'))->columnSpanFull(),
                                TextInput::make('app_app_store')->label(__('filament.app_store_url'))->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedArchiveBox),
                        
                        Tab::make('social_media')
                            ->label(__('filament.social_media'))
                            ->schema([
                                TextInput::make('app_facebook')->label(__('filament.facebook'))->url()->columnSpanFull(),
                                TextInput::make('app_twitter')->label(__('filament.twitter'))->url()->columnSpanFull(),
                                TextInput::make('app_whatsapp')->label(__('filament.whatsapp'))->columnSpanFull(),
                                TextInput::make('app_massenger')->label(__('filament.messenger'))->columnSpanFull(),
                                TextInput::make('app_instagram')->label(__('filament.instagram'))->url()->columnSpanFull(),
                                TextInput::make('app_youtube')->label(__('filament.youtube'))->url()->columnSpanFull(),
                                TextInput::make('app_tiktok')->label(__('filament.tiktok'))->url()->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedGlobeEuropeAfrica),

                        Tab::make('contact_info')
                            ->label(__('filament.contact_info'))
                            ->schema([
                                TextInput::make('contact_title')->label(__('filament.page_title'))->columnSpanFull(),
                                Textarea::make('contact_introduction')->label(__('filament.introduction'))->columnSpanFull(),
                                TextInput::make('contact_form_title')->label(__('filament.form_title'))->columnSpanFull(),
                                Textarea::make('contact_form_introduction')->label(__('filament.form_intro'))->columnSpanFull(),
                                TextInput::make('contact_address_title')->label(__('filament.address_title'))->columnSpanFull(),
                                Textarea::make('contact_address_content')->label(__('filament.address_content'))->columnSpanFull(),
                                TextInput::make('contact_email_title')->label(__('filament.email_title'))->columnSpanFull(),
                                TextInput::make('contact_email_content')->label(__('filament.email_content'))->columnSpanFull(),
                                TextInput::make('contact_phone_title')->label(__('filament.phone_title'))->columnSpanFull(),
                                TextInput::make('contact_phone_content')->label(__('filament.phone_content'))->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedEnvelope),

                        Tab::make('ads_banners')
                            ->label(__('filament.ads_banners'))
                            ->schema([
                                FileUpload::make('banner_1')->label(__('filament.banner').' 1')->image()->directory('public/files')
                    ->visibility('public')->columnSpanFull(),
                                TextInput::make('banner_1_link')->label(__('filament.banner_link').' 1')->url()->columnSpanFull(),
                                FileUpload::make('banner_2')->label(__('filament.banner').' 2')->image()->directory('public/files')
                    ->visibility('public')->columnSpanFull(),
                                TextInput::make('banner_2_link')->label(__('filament.banner_link').' 2')->url()->columnSpanFull(),
                                FileUpload::make('banner_3')->label(__('filament.banner').' 3')->image()->directory('public/files')
                    ->visibility('public')->columnSpanFull(),
                                TextInput::make('banner_3_link')->label(__('filament.banner_link').' 3')->url()->columnSpanFull(),
                            ])
                            ->icon(Heroicon::OutlinedTv),
                    ])->columnSpan('full')
            ]);
    }
}
