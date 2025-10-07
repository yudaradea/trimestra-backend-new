<?php

namespace App\Repositories;

use App\Interfaces\ExerciseRepositoryInterface;
use App\Models\Exercise;
use Exception;
use Illuminate\Support\Facades\DB;

class ExerciseRepository implements ExerciseRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, ?string $jenis, bool $execute,)
    {
        $query = Exercise::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->where('is_active', true);

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function getAllPaginated(?string $search, ?int $rowPerPage, ?string $jenis)
    {
        $query = $this->getAll($search, $rowPerPage, $jenis, false);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = Exercise::where('id', $id);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $exercise = Exercise::create($data);

            DB::commit();
            return $exercise;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $exercise = Exercise::find($id);
            $exercise->update($data);

            DB::commit();
            return $exercise;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $exercise = Exercise::find($id);
            $exercise->delete();

            DB::commit();
            return $exercise;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
