<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function provinces()
    {
        $provinces = Province::select('id', 'name')->get();
        return response()->json($provinces);
    }

    public function regencies(Request $request)
    {
        $regencies = Regency::where('province_id', $request->province_id)->select('id', 'name')->get();
        return response()->json($regencies);
    }

    public function districts(Request $request)
    {
        $districts = District::where('regency_id', $request->regency_id)->select('id', 'name')->get();
        return response()->json($districts);
    }

    public function villages(Request $request)
    {
        $villages = Village::where('district_id', $request->district_id)->select('id', 'name')->get();
        return response()->json($villages);
    }
}
