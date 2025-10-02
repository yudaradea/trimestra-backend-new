<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\FoodCategory\StoreRequest;
use App\Http\Requests\FoodCategory\UpdateRequest;
use App\Http\Resources\FoodCategoryResource;
use App\Interfaces\FoodCategoryRepositoryInterfaces;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FoodCategoryController extends Controller implements HasMiddleware
{
    private FoodCategoryRepositoryInterfaces $foodCategoryRepository;

    public function __construct(FoodCategoryRepositoryInterfaces $foodCategoryRepository)
    {
        $this->foodCategoryRepository = $foodCategoryRepository;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('admin', except: ['index', 'show']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $foodCategories = $this->foodCategoryRepository->getAll();
            return ResponseHelper::jsonResponse(true, 'Data Kategori Makanan Berhasil Diambil', FoodCategoryResource::collection($foodCategories), 200);
        } catch (Exception $e) {
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
            $foodCategory = $this->foodCategoryRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Kategori Makanan Berhasil Dibuat', FoodCategoryResource::make($foodCategory), 200);
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
            $foodCategory = $this->foodCategoryRepository->getById($id);

            if (!$foodCategory) {
                return ResponseHelper::jsonResponse(false, 'Data Kategori Makanan Tidak Ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Kategori Makanan Berhasil Diambil', FoodCategoryResource::make($foodCategory), 200);
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
            $foodCategory = $this->foodCategoryRepository->getById($id);

            if (!$foodCategory) {
                return ResponseHelper::jsonResponse(false, 'Data Kategori Makanan Tidak Ditemukan', null, 404);
            }

            $foodCategory = $this->foodCategoryRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Kategori Makanan Berhasil Diupdate', FoodCategoryResource::make($foodCategory), 200);
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
            $foodCategory = $this->foodCategoryRepository->getById($id);

            if (!$foodCategory) {
                return ResponseHelper::jsonResponse(false, 'Data Kategori Makanan Tidak Ditemukan', null, 404);
            }

            $this->foodCategoryRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Kategori Makanan Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
