<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\UserFood\StoreUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\UserFoodResource;
use App\Interfaces\UserFoodRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class UserFoodController extends Controller
{
    private UserFoodRepositoryInterface $userFoodRepository;
    public function __construct(UserFoodRepositoryInterface $userFoodRepository)
    {
        $this->userFoodRepository = $userFoodRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        try {
            $userFoods = $this->userFoodRepository->getAll(
                $user->id,
                $request->search,
                $request->limit,
                true
            );
            return ResponseHelper::jsonResponse(true, 'Data User Food berhasil diambil', UserFoodResource::collection($userFoods), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer'
        ]);
        try {
            $userFoods = $this->userFoodRepository->getAllPaginated(
                $user->id,
                $request->search,
                $request->row_per_page,
            );
            return ResponseHelper::jsonResponse(true, 'Data User Food berhasil diambil', PaginateResource::make($userFoods, UserFoodResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateRequest $request)
    {
        $request = $request->validated();

        try {
            $userFood = $this->userFoodRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'User Food Berhasil Dibuat', UserFoodResource::make($userFood), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $user = $request->user();
        try {
            $userFood = $this->userFoodRepository->getById($id, $user->id);
            if (!$userFood) {
                return ResponseHelper::jsonResponse(false, 'Data User Food tidak ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data User Food berhasil diambil', UserFoodResource::make($userFood), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateRequest $request, string $id)
    {
        $user = $request->user();
        $request = $request->validated();
        try {
            $userFood = $this->userFoodRepository->getById($id, $user->id);
            if (!$userFood) {
                return ResponseHelper::jsonResponse(false, 'Data User Food tidak ditemukan', null, 404);
            }
            $userFood = $this->userFoodRepository->update($id, $request, $user->id);
            return ResponseHelper::jsonResponse(true, 'User Food Berhasil Diupdate', UserFoodResource::make($userFood), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        $user = $request->user();
        try {
            $userFood = $this->userFoodRepository->getById($id, $user->id);
            if (!$userFood) {
                return ResponseHelper::jsonResponse(false, 'Data User Food tidak ditemukan', null, 404);
            }
            $this->userFoodRepository->delete($id, $user->id);
            return ResponseHelper::jsonResponse(true, 'User Food Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
