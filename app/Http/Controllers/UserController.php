<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterfaces;
use App\Services\NutritionService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Middleware\SubstituteBindings;

class UserController extends Controller implements HasMiddleware
{
    private UserRepositoryInterfaces $userRepository;
    private NutritionService $nutritionService;

    public function __construct(UserRepositoryInterfaces $userRepository, NutritionService $nutritionService)
    {
        $this->userRepository = $userRepository;
        $this->nutritionService = $nutritionService;
    }

    public static function middleware(): array
    {
        return [
            'admin',
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userRepository->getAll(
                $request->search,
                $request->limit,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diambil', UserResource::collection($users), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer',
        ]);

        try {
            $users = $this->userRepository->getAllPaginated(
                $request->search,
                $request->row_per_page,
                [
                    'province_id' => $request->province_id,
                    'regency_id'  => $request->regency_id,
                    'district_id' => $request->district_id,
                    'village_id'  => $request->village_id,
                ],
                $request->sort_by,
                $request->sort_direction
            );

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diambil', PaginateResource::make($users, UserResource::class), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $request = $request->validated();

        try {
            $user = $this->userRepository->create($request);

            // menambahkan bmi dan kategori bmi ke profile user baru
            $this->nutritionService->updateProfileAndNutrition($user->id);

            return ResponseHelper::jsonResponse(true, 'User Berhasil Dibuat', UserResource::make($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = $this->userRepository->getById($id);

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'User Tidak Ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data user berhasil diambil', UserResource::make($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $user = $this->userRepository->getById($id);

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'User Tidak Ditemukan', null, 404);
            }

            $user = $this->userRepository->update($id, $request);

            // update bmi yang ada di profile data dan update nutrition target juga
            $this->nutritionService->updateProfileAndNutrition($id);


            return ResponseHelper::jsonResponse(true, 'User Berhasil Diupdate', UserResource::make($user), 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = $this->userRepository->getById($id);

            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'User Tidak Ditemukan', null, 404);
            }

            $this->userRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'User Berhasil Dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
