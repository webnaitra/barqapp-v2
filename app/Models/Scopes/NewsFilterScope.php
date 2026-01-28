<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Country;

class NewsFilterScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $countryCode = request()->input('country');
            if ($countryCode) {
                $country = Country::where('country_code', $countryCode)->first();
            
            if ($country) {
                $builder->whereHas('sources.countries', function ($q) use ($country) {
                    $q->where('countries.id', $country->id);
                });
            }

            $user = auth('api')->check() ? auth('api')->user() : null;
            if($user) {
                $sourceFilterEnabled = getOptionValue('app_source_filter_enabled');
                $categoryFilterEnabled = getOptionValue('app_category_filter_enabled');

                if ($sourceFilterEnabled == 'Yes') {
                    $userSourceIds = $user->sources->pluck('id')->toArray();
                    if (!empty($userSourceIds)) {
                        $builder->whereIn('source_id', $userSourceIds);
                    }
                }
            }
        } 
    }
}
