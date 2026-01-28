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

    public function __construct($user, $newsItems)
    {
        $this->user = $user;
        $this->newsItems = $newsItems;
        $this->topArticle = optional($newsItems->first(), function($item) {
        return [
            'title' => $item->name,
            'date' => $item->date,
            'slug' => $item->slug,
            'summary' => $item->excerpt,
            'image' => $item->image,
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
            'image' => $item->image,
            'url' => $item->url,
            'section' => $item->category->name ?? null,
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
            'promoBannerUrl' => asset('assets/mail/mid-banner.png'),
            'androidBadgeUrl' => asset('assets/mail/android-app-store.png'),
            'iosBadgeUrl' => asset('assets/mail/apple-app-store.png'),
            'androidLink' => "#",
            'iosLink' => "#",
            'socialImageUrl' => asset('assets/mail/social-icons.png'),
            'topArticle' => $this->topArticle,
            'articles' => $this->articles,
            'social' => [
                'twitter' => ['icon' => asset('assets/mail/x-twitter.png'), 'link' => 'https://twitter.com/YourProfile'],
                'facebook' => ['icon' => asset('assets/mail/facebook-f.png'), 'link' => 'https://www.facebook.com/YourPage'],
                'instagram' => ['icon' => asset('assets/mail/instagram.png'), 'link' => 'https://www.instagram.com/YourProfile'],
                'youtube' => ['icon' => asset('assets/mail/youtube.png'), 'link' => 'https://www.youtube.com/YourChannel'],
                'tiktok' => ['icon' => asset('assets/mail/tiktok.png'), 'link' => 'https://www.tiktok.com/YourProfile'],
            ],
        ]);
            
    }
}

