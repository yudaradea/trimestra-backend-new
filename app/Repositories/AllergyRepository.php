<?php

namespace App\Repositories;

use App\Interfaces\AllergyRepositoryInterface;
use App\Models\Allergy;
use Exception;
use Illuminate\Support\Facades\DB;

class AllergyRepository implements AllergyRepositoryInterface
{
    public function getAll()
    {

        return Allergy::orderBy('created_at', 'desc')->get();
    }

    public function getById(string $id)
    {
        $query = Allergy::where('id', $id);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $allergy = Allergy::create($data);

            DB::commit();
            return $allergy;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $allergy = Allergy::find($id);
            $allergy->update($data);

            DB::commit();
            return $allergy;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $allergy = Allergy::find($id);
            $allergy->delete();

            DB::commit();
            return $allergy;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
