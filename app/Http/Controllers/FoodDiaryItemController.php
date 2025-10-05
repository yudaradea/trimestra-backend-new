<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\FoodDiaryItem\StoreUpdateRequest;
use App\Http\Resources\FoodDiaryItemResource;
use App\Models\FoodDiary;
use App\Models\FoodDiaryItem;
use Exception;
use Illuminate\Http\Request;

class FoodDiaryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        try {
            // pastikan food diary milik user
            $foodDiary = FoodDiary::where('id', $data['food_diary_id'])->where('user_id', $user->id)->first();

            if (!$foodDiary) {
                return ResponseHelper::jsonResponse(false, 'Data Food Diary tidak ditemukan', null, 404);
            }

            $item = $foodDiary->foodDiaryItem()->create([
                'food_id' => $data['food_id'] ?? null,
                'user_food_id' => $data['user_food_id'] ?? null,
                'quantity' => $data['quantity'],
            ]);

            return ResponseHelper::jsonResponse(true, 'Data berhasil disimpan', FoodDiaryItemResource::make($item), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
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
    public function update(StoreUpdateRequest $request, string $id)
    {
        $user = $request->user();
        $data = $request->validated();

        try {
            $item = FoodDiaryItem::where('id', $id)->whereHas('foodDiary', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->first();

            if (!$item) {
                return ResponseHelper::jsonResponse(false, 'Data Food Diary Item tidak ditemukan', null, 404);
            }

            $item->update([
                'food_id' => $data['food_id'] ?? null,
                'user_food_id' => $data['user_food_id'] ?? null,
                'quantity' => $data['quantity'],
            ]);

            return ResponseHelper::jsonResponse(true, 'Data berhasil disimpan', FoodDiaryItemResource::make($item), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500, [
                'errors' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $user = $request->user();
        try {
            $item = FoodDiaryItem::where('id', $id)->whereHas('foodDiary', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->first();

            if (!$item) {
                return ResponseHelper::jsonResponse(false, 'Data Food Diary Item tidak ditemukan', null, 404);
            }

            $foodDiaryId = $item->food_diary_id;

            $item->delete();

            // check apakah diary id masih punya item
            $itemCount = FoodDiaryItem::where('food_diary_id', $foodDiaryId)->count();
            if ($itemCount === 0) {
                FoodDiary::where('id', $foodDiaryId)->delete();
            }

            return ResponseHelper::jsonResponse(true, 'Data berhasil dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
