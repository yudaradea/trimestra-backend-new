<?php

namespace App\Repositories;

use App\Interfaces\UserFoodRepositoryInterface;
use App\Models\UserFood;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserFoodRepository implements UserFoodRepositoryInterface
{
    public function getAll($userId, ?string $search, ?int $limit, bool $execute)
    {
        $query = UserFood::where('user_id', $userId)->where(function ($query) use ($search) {
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
        $query = UserFood::where('id', $id)->where('user_id', $userId)->first();

        return $query;
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $userId = Auth::user()->id;
            $data['user_id'] = $userId;
            $userFood = UserFood::create($data);

            DB::commit();
            return $userFood;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data, $userId)
    {
        DB::beginTransaction();

        try {
            $userFood = UserFood::find($id)->where('user_id', $userId)->firstOrFail();
            $userFood->update($data);

            DB::commit();
            return $userFood;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id, $userId)
    {
        DB::beginTransaction();

        try {
            $userFood = UserFood::find($id)->where('user_id', $userId)->firstOrFail();
            $userFood->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
