<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Affiliate;
use App\Models\AdminAd;
use App\Models\Video;

class ViewCounterController extends Controller
{
    
    public function affiliate($id)
    {
        $id = base64_decode($id);
        $affiliate = Affiliate::findOrFail($id);
        $affiliate->increment('view_count');
        if(request()->type == 'json'){
           return response()->json([
                'message' => 'Success'
            ], 201);
        }
        return redirect($affiliate->url);
    }

    public function ads($id)
    {
        $id = base64_decode($id);
        $ad = AdminAd::findOrFail($id);
        $ad->increment('view_count');

        if(request()->type == 'json'){
           return response()->json([
                'message' => 'Success'
            ], 201);
        }
        return redirect($ad->url);
    }

    public function video($id)
    {
        $id = base64_decode($id);
        $video = Video::findOrFail($id);
        $video->increment('view_count');

        if(request()->type == 'json'){
           return response()->json([
                'message' => 'Success'
            ], 201);
        }
        return redirect($video->video);
    }


    public function testGoldPrice()
    {
        $baseCurrency = 'USD';
        $goldUrl = env('GOLD_RATE_PROXY_URL', '') . "?currency=EUR";
        $goldResponse = \Illuminate\Support\Facades\Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->withOptions(['verify' => false])->get($goldUrl);

        if ($goldResponse->successful()) {
            return response()->json($goldResponse->json());
        } else {
            return response()->json(['error' => 'Failed to fetch gold price'], 500);
        }
    }
}
