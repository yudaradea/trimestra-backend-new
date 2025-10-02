<?php

namespace App\Repositories;

use App\Interfaces\NutritionRequirementsRepositoryInterfaces;
use App\Models\NutritionRequirement;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class NutritionRequirementsRepository implements NutritionRequirementsRepositoryInterfaces
{
    public function getAll()
    {
        return NutritionRequirement::orderBy('created_at', 'desc')->get();
    }

    public function getById(string $id)
    {
        $query = NutritionRequirement::where('id', $id);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {

            $nutritionRequirement = NutritionRequirement::create($data);

            DB::commit();
            return $nutritionRequirement;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $nutritionRequirement = NutritionRequirement::find($id);
            $nutritionRequirement->update($data);

            DB::commit();
            return $nutritionRequirement;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $nutritionRequirement = NutritionRequirement::find($id);
            $nutritionRequirement->delete();

            DB::commit();
            return $nutritionRequirement;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
