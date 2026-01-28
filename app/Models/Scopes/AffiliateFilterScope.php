<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Models\Country;

class AffiliateFilterScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $countryCode = request()->input('country');
        
        if ($countryCode) {
            $country = Country::where('country_code', $countryCode)->first();
            
            if ($country) {
                $builder->where('country_id', $country->id);
            }
        }
    }
}