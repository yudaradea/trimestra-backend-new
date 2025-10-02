<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterfaces;
use App\Services\NutritionService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    private UserRepositoryInterfaces $userRepository;
    private NutritionService $nutritionService;

    public function __construct(UserRepositoryInterfaces $userRepository, NutritionService $nutritionService)
    {
        $this->userRepository = $userRepository;
        $this->nutritionService = $nutritionService;
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
            $user = $this->userRepository->update($user->id, $updateRequest);
            $profile = $user->profile;

            // update bmi yang ada di profile data dan update nutrition target juga
            $this->nutritionService->updateProfileAndNutrition($user->id);

            return ResponseHelper::jsonResponse(true, 'Profile Berhasil Diupdate', UserResource::make($user, $profile), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
