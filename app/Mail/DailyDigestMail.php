<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $newsItems;
    public $settings;

    public function __construct($user, $newsItems)
    {
        $this->user = $user;
        $this->newsItems = $newsItems;
        $this->settings = app(\App\Settings\GeneralSettings::class);
        $this->topArticle = optional($newsItems->first(), function($item) {
        return [
            'title' => $item->name,
            'date' => $item->date,
            'slug' => $item->slug,
            'summary' => $item->excerpt,
            'image' => $item->image_url,
            'url' => $item->url,
            'label' => 'عاجل',
        ];
    });

    $this->articles = $newsItems->skip(1)->take(6)->map(function($item){
        return [
            'title' => $item->name,
            'date' => $item->date,
            'slug' => $item->slug,
            'summary' => $item->excerpt,
            'image' => $item->image_url,
            'url' => $item->url,
            'section' => $item->category->arabic_name ?? null,
        ];
    })->values()->all();

    }

    public function build()
    {
        return $this->subject('Your Daily News Digest')
            ->view('emails.daily_digest')
            ->with([
            'formattedDate' => now()->locale('ar')->translatedFormat('d MMMM y'),
            'logoUrl' => asset('assets/mail/logo.png'),
            'promoBannerUrl' => asset('assets/mail/mid-banner.png'), // Keeping just in case, but likely replacing usage
            'androidBadgeUrl' => asset('assets/mail/android-app-store.png'),
            'iosBadgeUrl' => asset('assets/mail/apple-app-store.png'),
            'androidLink' => $this->settings->app_google_play,
            'iosLink' => $this->settings->app_app_store,
            'appHeader' => $this->settings->app_header,
            'appDownloadText' => $this->settings->app_download_text,
            'socialImageUrl' => asset('assets/mail/social-icons.png'),
            'topArticle' => $this->topArticle,
            'articles' => $this->articles,
            'social' => [
                'twitter' => ['icon' => asset('assets/mail/x-twitter.png'), 'link' => $this->settings->app_twitter],
                'facebook' => ['icon' => asset('assets/mail/facebook-f.png'), 'link' => $this->settings->app_facebook],
                'instagram' => ['icon' => asset('assets/mail/instagram.png'), 'link' => $this->settings->app_instagram],
                'youtube' => ['icon' => asset('assets/mail/youtube.png'), 'link' => $this->settings->app_youtube],
                'tiktok' => ['icon' => asset('assets/mail/tiktok.png'), 'link' => $this->settings->app_tiktok],
            ],
        ]);
            
    }
}

