<?php

namespace App\Repositories;

use App\Interfaces\FoodDiaryRepositoryInterface;
use App\Models\FoodDiary;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FoodDiaryRepository implements FoodDiaryRepositoryInterface
{
    public function getAll($userId, ?string $search, ?int $limit, ?string $filterByDate, bool $execute)
    {
        $query = FoodDiary::where('user_id', $userId)->where(function ($query) use ($search, $filterByDate) {
            if ($search) {
                $query->search($search);
            }
            if ($filterByDate) {
                $query->where('date', $filterByDate);
            }
        })->with('foodDiaryItem.food', 'foodDiaryItem.userFood');

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function getAllPaginated($userId, ?string $search, ?int $rowPerPage)
    {
        $query = $this->getAll($userId, $search, $rowPerPage, null, false);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id, $userId)
    {
        $query = FoodDiary::where('id', $id)->with('foodDiaryItem.food', 'foodDiaryItem.userFood')->where('user_id', $userId);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::user()->id;
            $data['user_id'] = $userId;
            $foodDiary = FoodDiary::updateOrCreate([
                'user_id' => $userId,
                'date' => $data['date'],
                'type' => $data['type'],
            ]);

            // memasukan data item makanan
            foreach ($data['items'] as $item) {
                $foodDiary->foodDiaryItem()->create([
                    'food_diary_id' => $foodDiary->id,
                    'food_id' => $item['food_id'] ?? null,
                    'user_food_id' => $item['user_food_id'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();
            return $foodDiary;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data, $userId)
    {
        DB::beginTransaction();

        try {
            $foodDiary = FoodDiary::find($id)->where('user_id', $userId)->firstOrFail();
            $foodDiary->update([
                'date' => $data['date'],
                'type' => $data['type'],
            ]);

            // hapus item makanan
            $foodDiary->foodDiaryItem()->delete();

            // memasukan data item makanan
            foreach ($data['food_diary_item'] as $item) {
                $foodDiary->foodDiaryItem()->Create([
                    'food_diary_id' => $foodDiary->id,
                    'food_id' => $item['food_id'] ?? null,
                    'user_food_id' => $item['user_food_id'] ?? null,
                    'quantity' => $item['quantity'],
                ]);
            }
            DB::commit();
            return $foodDiary;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id, $userId)
    {
        DB::beginTransaction();

        try {
            $foodDiary = FoodDiary::find($id)->where('user_id', $userId)->firstOrFail();
            $foodDiary->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
