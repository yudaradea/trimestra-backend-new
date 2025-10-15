<?php

namespace App\Repositories;

use App\Interfaces\FoodCategoryRepositoryInterfaces;
use App\Models\FoodCategory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class FoodCategoryRepository implements FoodCategoryRepositoryInterfaces
{
    public function getAll()
    {
        return FoodCategory::orderBy('order', 'asc')->get();
    }

    public function getById(string $id)
    {
        return FoodCategory::find($id);
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $foodCategory = FoodCategory::create($data);

            if (isset($data['icon']) && $data['icon']->isValid()) {
                $iconPath = $data['icon']->store('assets/food_category', 'public');
                $foodCategory->icon = $iconPath;
            }

            $foodCategory->save();

            DB::commit();
            return $foodCategory;
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($iconPath)) {
                Storage::disk('public')->delete($iconPath);
            }
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $foodCategory = FoodCategory::find($id);
            $foodCategory->update($data);

            // jika ada perubahan icon maka hapus icon lama
            if (isset($data['icon']) && $data['icon']->isValid()) {
                if ($foodCategory->icon) {
                    Storage::disk('public')->delete($foodCategory->icon);
                }
                $iconPath = $data['icon']->store('assets/food_category', 'public');
                $foodCategory->icon = $iconPath;
                $foodCategory->save();
            }

            DB::commit();
            return $foodCategory;
        } catch (Exception $e) {
            DB::rollBack();
            if (isset($iconPath)) {
                Storage::disk('public')->delete($iconPath);
            }
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $foodCategory = FoodCategory::find($id);
            $foodCategory->delete();

            // hapus icon jika ada
            if ($foodCategory->icon) {
                Storage::disk('public')->delete($foodCategory->icon);
            }

            DB::commit();
            return $foodCategory;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
