<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\WeightLogResource;
use App\Models\WeightLog;
use Illuminate\Http\Request;

class WeightLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $weightLogs = WeightLog::where('user_id', $user->id)->orderBy('date', 'desc')->get();

        return ResponseHelper::jsonResponse(true, 'Data Berhasil Diambil', WeightLogResource::collection($weightLogs), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
