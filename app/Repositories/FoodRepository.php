<?php

namespace App\Repositories;

use App\Interfaces\FoodRepositoryInterface;
use App\Models\Food;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FoodRepository implements FoodRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, ?string $filterByFoodCategoryId, bool $execute)
    {
        $query = Food::where(function ($query) use ($search, $filterByFoodCategoryId) {
            if ($search) {
                $query->search($search);
            }
            if ($filterByFoodCategoryId) {
                $query->where('food_category_id', $filterByFoodCategoryId);
            }
        })->with('foodCategory');

        $query->orderBy('created_at', 'desc');

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(?string $search, ?int $rowPerPage, ?string $filterByFoodCategoryId)
    {
        $query = $this->getAll($search, $rowPerPage, $filterByFoodCategoryId, false);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = Food::where('id', $id)->with('foodCategory');

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $food = Food::create($data);

            // jika menambahkan gambar
            if (isset($data['image']) && $data['image']->isValid()) {
                $imagePath = $data['image']->store('assets/food', 'public');
                $food->image = $imagePath;
                $food->save();
            }

            DB::commit();
            return $food;
        } catch (Exception $e) {
            DB::rollBack();
            // jika gagal image hapus
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $food = Food::find($id);
            $food->update($data);

            // jika ada perubahan image maka hapus image lama
            if (isset($data['image']) && $data['image']->isValid()) {
                if ($food->image) {
                    Storage::disk('public')->delete($food->image);
                }
                $imagePath = $data['image']->store('assets/food', 'public');
                $food->image = $imagePath;
                $food->save();
            }

            DB::commit();
            return $food;
        } catch (Exception $e) {
            DB::rollBack();
            // jika gagal image hapus
            if (isset($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $food = Food::find($id);
            $food->delete();

            // hapus image jika ada
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }
            DB::commit();
            return $food;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
