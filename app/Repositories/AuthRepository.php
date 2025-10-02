<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use App\Models\WeightLog;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthRepository implements AuthRepositoryInterface
{
    public function register(array $data)
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

            //    menambahkan path untuk foto profile jika registrasi berhasil
            if (isset($data['foto_profile']) && $data['foto_profile']->isValid()) {
                // Simpan path ke variabel yang sudah didefinisikan di atas
                $profilePhotoPath = $data['foto_profile']->store('assets/profile', 'public');
                $profileData['foto_profile'] = $profilePhotoPath;
            }

            //    Buat profile menggunakan relasi eloquent
            $user->profile()->create($profileData);


            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            if ($profilePhotoPath) {
                Storage::disk('public')->delete($profilePhotoPath);
            }
            throw new Exception($e->getMessage());
        }
    }


    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            return $user;
        }
        // 
        return null;
    }

    public function logout($user)
    {
        $user->currentAccessToken()->delete();
    }
}
