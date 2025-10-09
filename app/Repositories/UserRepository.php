<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterfaces;
use App\Models\Profile;
use App\Models\User;
use App\Models\WeightLog;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserRepository implements UserRepositoryInterfaces
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = User::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        })->with(['profile', 'nutritionTargets']);

        if ($limit) {
            $query->take($limit);
        }

        if ($execute) {
            return $query->get();
        }

        return $query->orderBy('created_at', 'desc');
    }
    public function getAllPaginated(?string $search, ?int $rowPerPage, ?array $filters = [], ?string $sortBy = null, ?string $sortDirection = 'asc')
    {
        $query = $this->getAll($search, null, false);

        // Filter lokasi berdasarkan kolom di tabel profiles
        if (!empty($filters['province_id'])) {
            $query->whereHas('profile', function ($q) use ($filters) {
                $q->where('province_id', $filters['province_id']);
            });
        }

        if (!empty($filters['regency_id'])) {
            $query->whereHas('profile', function ($q) use ($filters) {
                $q->where('regency_id', $filters['regency_id']);
            });
        }

        if (!empty($filters['district_id'])) {
            $query->whereHas('profile', function ($q) use ($filters) {
                $q->where('district_id', $filters['district_id']);
            });
        }

        if (!empty($filters['village_id'])) {
            $query->whereHas('profile', function ($q) use ($filters) {
                $q->where('village_id', $filters['village_id']);
            });
        }

        // sorting
        if ($sortBy) {
            $direction = $sortDirection ?? 'asc';

            if ($sortBy === 'location') {
                // urutkan berdasarkan province_id dari tabel profiles
                $query->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->orderBy('profiles.province_id', $direction)
                    ->select('users.*'); // penting biar tidak bentrok kolom
            } elseif ($sortBy === 'role') {
                $query->orderBy('role', $direction);
            } else {
                $query->orderBy($sortBy, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($rowPerPage);
    }
    public function getById(string $id)
    {
        $query = User::where('id', $id)->with(['profile', 'nutrition_targets']);

        return $query->first();
    }

    public function create(array $data)
    {
        DB::beginTransaction();

        try {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ];

            $user = User::create($userData);

            // Menggunakan Arr::except untuk mengambil semua data kecuali data user
            $profileData = Arr::except($data, ['name', 'email', 'password']);

            if (isset($profileData['weight'])) {
                WeightLog::updateOrCreate([
                    'user_id' => $user->id,
                    'date' => now()->toDateString(),
                ], [

                    'weight' => $profileData['weight'],
                ]);
            }

            //    menambahkan path untuk foto profile
            $profileData['foto_profile'] = $data['foto_profile']->store('assets/profile', 'public');

            //    Buat profile menggunakan relasi eloquent
            $user->profile()->create($profileData);


            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);
            $userData = Arr::only($data, ['name', 'email', 'password']);
            // jika ada perubahan password maka hash password baru
            if (isset($userData['password'])) {
                $userData['password'] = bcrypt($userData['password']);
            }
            $user->update($userData);

            $profileData = Arr::except($data, ['name', 'email']);


            // menyimpan log berat badan jika ada perubahan
            if (isset($profileData['weight'])) {
                WeightLog::updateOrCreate([
                    'user_id' => $user->id,
                    'date' => now()->toDateString(),
                ], [

                    'weight' => $profileData['weight'],
                ]);
            }

            // menghapus foto lama jika ada pergantian foto profile
            if (isset($profileData['foto_profile'])) {
                // hapus foto lama
                if ($user->profile && $user->profile->foto_profile) {
                    Storage::disk('public')->delete($user->profile->foto_profile);
                }
                $profileData['foto_profile'] = $data['foto_profile']->store('assets/profile', 'public');
            }
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();

        try {
            $user = User::find($id);

            // jika ada foto profile hapus
            if ($user->profile && $user->profile->foto_profile) {
                Storage::disk('public')->delete($user->profile->foto_profile);
            }

            $user->delete();
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
}
