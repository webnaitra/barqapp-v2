<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>نشرة بريدية</title>
  <style>
    @media only screen and (max-width: 600px) {
      .container { width: 100% !important; }
      .stack { display: block !important; width: 100% !important; max-width: 100% !important; }
      .stack img { max-width: 120px !important; width: 100% !important; height: 120px !important; }
      .content { padding: 12px !important; }
      .headline { font-size: 18px !important; line-height: 24px !important; }
      .subheadline { font-size: 14px !important; line-height: 20px !important; }
      .footer-text { font-size: 12px !important; line-height: 18px !important; }
    }
  </style>
</head>
<body style="margin:0; padding:0; background:#fff; direction:rtl; text-align:right;">
  <center style="width:100%; background:#fff;">
    <table role="presentation" class="container" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff" style="max-width: 800px; width:100%;">
      <tr>
        <td align="center">
          <!-- Wrapper -->
          <table role="presentation" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" width="100%">

            <!-- Header -->
            <tr>
              <td align="center" style="padding:28px 20px 8px 20px;">
                <img src="{{ $logoUrl }}" width="120" height="120" alt="Logo" style="display:block; max-width:120px;">
              </td>
            </tr>
            <tr>
              <td align="center" style="padding:0 20px 16px 20px; font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:17px; font-weight:500; line-height:20px; color:#7D7D7D; margin-top:20px;">
                {{ $formattedDate }}
              </td>
            </tr>

            @if(isset($topArticle))
            <!-- Top Article Item -->
            <tr>
              <td class="content" style="padding:16px 20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                    @php
                      $date = $topArticle['date'];
                      $dt = \Carbon\Carbon::parse($date);
                      $url = env('FRONTEND_URL') . '/' . $dt->year . '/' . $dt->month . '/' . $dt->day . '/' . $topArticle['slug'];
                    @endphp
                    <!-- Thumbnail -->
                    <td class="stack" valign="top" width="140" style="width:140px;">
                      <a href="{{ $url }}"><img src="{{ $topArticle['image'] }}" width="140" height="140" alt="" style="display:block; max-width:100%; height:auto;"></a>
                    </td>
                    <!-- Text -->
                    <td class="stack" valign="top" style="padding-right:12px;">
                      @if(!empty($topArticle['label']))
                        <div style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:16px; color:#CA2B2E; line-height:16px; margin-bottom:20px; font-weight:800;">
                          {{ $topArticle['label'] }}
                        </div>
                      @endif
                      <a href="{{ $url }}" class="headline" style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:20px; line-height:22px; color:#111111; text-decoration:none; display:block; font-weight:800;">
                        {{ $topArticle['title'] }}
                      </a>
                      @if(!empty($topArticle['summary']))
                        <div style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; color:#333; margin-top:8px;">
                          {{ $topArticle['summary'] }}
                        </div>
                      @endif
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr><td style="border-top:1px solid #eeeeee;"></td></tr>
            @endif

            <!-- App Banner (Red Box Design) -->
            <tr>
              <td style="padding:20px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#CA2B2E" background-repeat="no-repeat" background="{{ asset('assets/mail/app_banner_news-bg-g.png') }}" style="border-radius:12px; background-color:#CA2B2E; direction:ltr; background-repeat: no-repeat; background-size: cover;">
                  <tr>
                    <td style="padding:24px;">
                      
                      <!-- Header & Text -->
                      <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td align="right" >
                           <div style="background-color: #ca2b2e;font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:24px; font-weight:800; color:#FFFFFF; padding-bottom:12px; line-height:30px; display: inline-block; padding: 10px; border-radius: 5px; margin-bottom: 10px;"> {{ $appHeader ?? 'تطبيق عربي الإخباري' }}</div>
                          </td>
                        </tr>
                        <tr>
                          <td align="right" >
                           <div style="background-color: #ca2b2e;font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:16px; font-weight:500; color:#FFFFFF; line-height:22px; padding-bottom:24px; display: inline-block; padding: 10px; border-radius: 5px; margin-bottom: 10px;"> {{ $appDownloadText ?? 'حمّل التطبيق الآن' }}</div>
                          </td>
                        </tr>
                      </table>

                      <!-- Buttons -->
                      <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                          <td style="padding-right:12px;">
                            <a href="{{ $androidLink ?? '#' }}" target="_blank" style="text-decoration:none;">
                              <img src="{{ $androidBadgeUrl }}" width="135" height="40" alt="Google Play" style="display:block; border:0; max-width:135px; height:auto;">
                            </a>
                          </td>
                          <td>
                            <a href="{{ $iosLink ?? '#' }}" target="_blank" style="text-decoration:none;">
                              <img src="{{ $iosBadgeUrl }}" width="135" height="40" alt="App Store" style="display:block; border:0; max-width:135px; height:auto;">
                            </a>
                          </td>
                        </tr>
                      </table>

                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            @if(!empty($articles))
              @foreach($articles as $article)
                <!-- Article Item -->
                <tr>
                  <td class="content" style="padding:16px 20px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr>
                        <td class="stack" width="140" valign="top">
                          @php
                            $date = $article['date'];
                            $dt = \Carbon\Carbon::parse($date);
                            $url = env('FRONTEND_URL') . '/' . $dt->year . '/' . $dt->month . '/' . $dt->day . '/' . $article['slug'];
                          @endphp
                          <a href="{{ $url }}"><img src="{{ $article['image'] }}" width="140" height="140" alt="" style="display:block; max-width:100%; height:auto;"></a>
                        </td>
                        <td class="stack" valign="top" style="padding-right:12px;">
                          @if(!empty($article['section']))
                            <div style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:16px; color:#CA2B2E; margin-bottom:12px; font-weight:700;">
                              {{ $article['section'] }}
                            </div>
                          @endif
                          <a href="{{ $url }}" class="headline" style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:22px; line-height:22px; color:#111111; text-decoration:none; display:block; font-weight:800;">
                            {{ $article['title'] }}
                          </a>
                          @if(!empty($article['summary']))
                            <div style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; color:#333; margin-top:8px;">
                              {{ $article['summary'] }}
                            </div>
                          @endif
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr><td style="border-top:1px solid #eeeeee;"></td></tr>
              @endforeach
            @endif

            <!-- Footer -->
            <tr>
              <td align="center" style="padding:28px 20px 0 20px;">
                <div style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:32px; line-height:24px; color:#000; font-weight:800;">تطبيق عربي الإخباري</div>
                <div class="subheadline" style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:20px; line-height:20px; color:#1E1E1E; margin-top:10px; font-weight:500;">
                  حمّل التطبيق الآن لتحصل على تحديثات فورية
                </div>
              </td>
            </tr>
            <tr>
              <td align="center" style="padding:16px 20px 28px 20px;">
                @if(!empty($androidBadgeUrl))
                  <a href="{{ $androidLink ?? '#' }}"><img src="{{ $androidBadgeUrl }}" width="130" height="40" alt="Google Play" style="display:inline-block; max-width:130px; height:auto;"></a>
                @endif
                @if(!empty($iosBadgeUrl))
                  <a href="{{ $iosLink ?? '#' }}"><img src="{{ $iosBadgeUrl }}" width="130" height="40" alt="App Store" style="display:inline-block; margin:0 6px; max-width:130px; height:auto;"></a>
                @endif
              </td>
            </tr>

            <!-- Social + Legal -->
            <tr>
              <td align="center" style="padding:20px;">
                @if(isset($social['facebook']))
                  <a href="{{ $social['facebook']['link'] }}"><img src="{{ $social['facebook']['icon'] }}" height="35" style="display:inline-block; margin:0 6px; max-height:35px;"></a>
                @endif
                @if(isset($social['twitter']))
                  <a href="{{ $social['twitter']['link'] }}"><img src="{{ $social['twitter']['icon'] }}" height="35" style="display:inline-block; margin:0 6px; max-height:35px;"></a>
                @endif
                @if(isset($social['instagram']))
                  <a href="{{ $social['instagram']['link'] }}"><img src="{{ $social['instagram']['icon'] }}" height="35" style="display:inline-block; margin:0 6px; max-height:35px;"></a>
                @endif
                @if(isset($social['youtube']))
                  <a href="{{ $social['youtube']['link'] }}"><img src="{{ $social['youtube']['icon'] }}" height="35" style="display:inline-block; margin:0 6px; max-height:35px;"></a>
                @endif
                @if(isset($social['tiktok']))
                  <a href="{{ $social['tiktok']['link'] }}"><img src="{{ $social['tiktok']['icon'] }}" height="35" style="display:inline-block; margin:0 6px; max-height:35px;"></a>
                @endif
                <div class="footer-text" style="font-family:Tajawal, Arial, Helvetica, sans-serif; font-size:14px; color:#1E1E1E; font-weight:500; line-height:18px; margin-top:10px; max-width:250px;">
                  الشروط وسياسة الخصوصية | لوحة معلومات الخصوصية | يعلن حول إعلاناتنا | وظائفي <br>
                  © {{ date('Y') }} خبر الإخبارية. كل الحقوق محفوظة.
                </div>
              </td>
            </tr>

          </table>
          <!-- /Wrapper -->
        </td>
      </tr>
    </table>
  </center>
</body>
</html>
