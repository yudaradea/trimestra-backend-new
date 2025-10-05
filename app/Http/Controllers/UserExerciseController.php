<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\UserExercise\StoreUpdateRequest;
use App\Http\Resources\PaginateResource;
use App\Http\Resources\UserExerciseResource;
use App\Interfaces\UserExerciseRepositoryInterface;
use Exception;
use Illuminate\Http\Request;

class UserExerciseController extends Controller
{
    private UserExerciseRepositoryInterface $userExerciseRepository;

    public function __construct(UserExerciseRepositoryInterface $userExerciseRepository)
    {
        $this->userExerciseRepository = $userExerciseRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        try {
            $userExercises = $this->userExerciseRepository->getAll(
                $user->id,
                $request->search,
                $request->limit,
                true
            );
            return ResponseHelper::jsonResponse(true, 'Data User Exercise berhasil diambil', UserExerciseResource::collection($userExercises), 200);
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
            $userExercises = $this->userExerciseRepository->getAllPaginated(
                $user->id,
                $request->search,
                $request->row_per_page,
            );
            return ResponseHelper::jsonResponse(true, 'Data User Exercise berhasil diambil', PaginateResource::make($userExercises, UserExerciseResource::class), 200);
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
            $userExercise = $this->userExerciseRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'User Exercise Berhasil Dibuat', UserExerciseResource::make($userExercise), 200);
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
            $userExercise = $this->userExerciseRepository->getById($id, $user->id);
            if (!$userExercise) {
                return ResponseHelper::jsonResponse(false, 'Data User Exercise tidak ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data User Exercise berhasil diambil', UserExerciseResource::make($userExercise), 200);
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
            $userExercise = $this->userExerciseRepository->getById($id, $user->id);
            if (!$userExercise) {
                return ResponseHelper::jsonResponse(false, 'Data User Exercise tidak ditemukan', null, 404);
            }
            $userExercise = $this->userExerciseRepository->update($id, $request, $user->id);
            return ResponseHelper::jsonResponse(true, 'User Exercise Berhasil Diupdate', UserExerciseResource::make($userExercise), 200);
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
            $userExercise = $this->userExerciseRepository->getById($id, $user->id);
            if (!$userExercise) {
                return ResponseHelper::jsonResponse(false, 'Data User Exercise tidak ditemukan', null, 404);
            }
            $this->userExerciseRepository->delete($id, $user->id);
            return ResponseHelper::jsonResponse(true, 'User Exercise Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
