<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Exercise\StoreRequest;
use App\Http\Requests\Exercise\UpdateRequest;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\ExerciseRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class Exercisecontroller extends Controller implements HasMiddleware
{
    private ExerciseRepositoryInterface $exerciseRepository;

    public function __construct(ExerciseRepositoryInterface $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('admin', except: ['index', 'show', 'getAllPaginated']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $exercises = $this->exerciseRepository->getAll(
                $request->search,
                $request->limit,
                $request->jenis,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data latihan berhasil diambil', ExerciseResource::collection($exercises), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',

            'row_per_page' => 'required|integer',
            'jenis' => 'nullable|string',
        ]);

        try {
            $exercises = $this->exerciseRepository->getAllPaginated(
                $request->search,
                $request->row_per_page ?? 10,
                $request->jenis ?? '',
            );

            return ResponseHelper::jsonResponse(true, 'Data latihan berhasil diambil', PaginateResource::make($exercises, ExerciseResource::class), 200);
        } catch (Exception $e) {
            Log::error('Exercise pagination error: ' . $e->getMessage(), [
                'search' => $request->search,
                'jenis' => $request->jenis,
                'row_per_page' => $request->row_per_page,
            ]);
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }

        $user = $request->user();
        if (!$user) {
            Log::error('User tidak terautentikasi');
            return ResponseHelper::jsonResponse(false, 'Unauthorized', null, 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $request = $request->validated();

        try {
            $exercise = $this->exerciseRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Latihan Berhasil Dibuat', ExerciseResource::make($exercise), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $exercise = $this->exerciseRepository->getById($id);
            if (!$exercise) {
                return ResponseHelper::jsonResponse(false, 'Data latihan tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data latihan berhasil diambil', ExerciseResource::make($exercise), 200);
        } catch (Exception $e) {
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
            $exercise = $this->exerciseRepository->getById($id);

            if (!$exercise) {
                return ResponseHelper::jsonResponse(false, 'Data latihan tidak ditemukan', null, 404);
            }

            $exercise = $this->exerciseRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Latihan Berhasil Diupdate', ExerciseResource::make($exercise), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $exercise = $this->exerciseRepository->getById($id);

            if (!$exercise) {
                return ResponseHelper::jsonResponse(false, 'Data latihan tidak ditemukan', null, 404);
            }

            $this->exerciseRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Latihan Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
