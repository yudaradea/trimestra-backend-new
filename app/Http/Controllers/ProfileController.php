<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Profile\StoreRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterfaces;
use App\Models\WeightLog;
use App\Services\NutritionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    private UserRepositoryInterfaces $userRepository;
    private NutritionService $nutritionService;

    public function __construct(UserRepositoryInterfaces $userRepository, NutritionService $nutritionService)
    {
        $this->userRepository = $userRepository;
        $this->nutritionService = $nutritionService;
    }

    public function store(StoreRequest $request)
    {
        $user = $request->user();
        $request = $request->validated();
        DB::beginTransaction();
        try {

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'Unauthenticated', null, 401);
            }


            // menambahkan path untuk menyimpan foto
            if (isset($request['foto_profile']) && $request['foto_profile']->isValid()) {
                // Simpan path ke variabel yang sudah didefinisikan di atas
                $profilePhotoPath = $request['foto_profile']->store('assets/profile', 'public');

                $request['foto_profile'] = $profilePhotoPath;
            }

            $user->profile()->create($request);

            if (isset($request['weight'])) {
                WeightLog::updateOrCreate([
                    'user_id' => $user->id,
                    'date' => now()->toDateString(),
                ], [

                    'weight' => $request['weight'],
                ]);
            }

            DB::commit();
            $profile = $user->profile;
            // menambahkan bmi dan kategori bmi ke profile user baru
            $this->nutritionService->updateProfileAndNutrition($user->id);

            return ResponseHelper::jsonResponse(true, 'Profile Berhasil Ditambahkan', UserResource::make($user, $profile), 200);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($profilePhotoPath)) {
                Storage::disk('public')->delete($profilePhotoPath);
            }
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'Unauthenticated', null, 401);
            }

            $profile = $user->profile;
            $nutrition_target = $user->nutritionTargets;

            return ResponseHelper::jsonResponse(true, 'Profile Berhasil Diambil', UserResource::make($user, $profile, $nutrition_target), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function update(Request $request, UpdateProfileRequest $updateRequest)
    {
        $updateRequest = $updateRequest->validated();


        try {
            $user = $request->user();

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'User Tidak Ditemukan', null, 404);
            }

            // jika dari tadinya hamil dirubah menjadi tidak hamil, maka hapus weeks, trimester, dan hpht
            if ($updateRequest['is_pregnant'] == false) {
                $updateRequest['weeks'] = null;
                $updateRequest['trimester'] = null;
                $updateRequest['hpht'] = null;
            }
            $user = $this->userRepository->update($user->id, $updateRequest);
            $profile = $user->profile;

            // update bmi yang ada di profile data dan update nutrition target juga
            $this->nutritionService->updateProfileAndNutrition($user->id);

            return ResponseHelper::jsonResponse(true, 'Profile Berhasil Diupdate', new UserResource($user, $profile), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
