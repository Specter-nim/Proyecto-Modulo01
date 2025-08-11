<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use App\Models\Province;
use App\Models\District;

class UbigeoController extends Controller
{
    public function regions()
    {
        return response()->json(Region::select('id', 'name')->get());
    }

    public function provinces($regionId)
    {
        return response()->json(
            Province::where('region_id', $regionId)->select('id', 'name')->get()
        );
    }

    public function districts($provinceId)
    {
        return response()->json(
            District::where('province_id', $provinceId)->select('id', 'name')->get()
        );
    }

    public function countries()
    {
        return response()->json(Country::select('id', 'name','phone_code', 'ISO2')->get());
    }
}
