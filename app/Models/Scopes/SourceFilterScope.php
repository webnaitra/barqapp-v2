<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Country;

class SourceFilterScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $countryCode = request()->input('country');
        if ($countryCode) {
            $country = Country::where('country_code', $countryCode)->first();
        
            if ($country) {
                $builder->whereHas('countries', function ($q) use ($country) {
                    $q->where('countries.id', $country->id);
                });
            }
        }

    }
}