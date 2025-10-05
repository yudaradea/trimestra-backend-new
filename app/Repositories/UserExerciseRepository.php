<?php

namespace App\Repositories;

use App\Interfaces\UserExerciseRepositoryInterface;
use App\Models\UserExercise;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserExerciseRepository implements UserExerciseRepositoryInterface
{
    public function getAll($userId, ?string $search, ?int $limit, bool $execute)
    {
        $query = UserExercise::where('user_id', $userId)->where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->orderBy('created_at', 'desc');

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
        $query = $this->getAll($userId, $search, $rowPerPage, false);

        return $query->paginate($rowPerPage);
    }

    public function getById(string $id, $userId)
    {
        $query = UserExercise::where('id', $id)->where('user_id', $userId)->first();

        return $query;
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::user()->id;
            $data['user_id'] = $userId;
            $userExercise = UserExercise::create($data);

            DB::commit();
            return $userExercise;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data, $userId)
    {
        DB::beginTransaction();

        try {
            $userExercise = UserExercise::where('id', $id)->where('user_id', $userId)->firstOrFail();
            $userExercise->update($data);

            DB::commit();
            return $userExercise;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id, $userId)
    {
        DB::beginTransaction();

        try {
            $userExercise = UserExercise::where('id', $id)->where('user_id', $userId)->firstOrFail();
            $userExercise->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
