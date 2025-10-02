<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\NutritionRequirement\StoreRequest;
use App\Http\Requests\NutritionRequirement\UpdateRequest;
use App\Http\Resources\NutritionRequirementResource;
use App\Interfaces\NutritionRequirementsRepositoryInterfaces;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class NutritionRequirementController extends Controller implements HasMiddleware
{
    private NutritionRequirementsRepositoryInterfaces $nutritionRequirementsRepository;

    public function __construct(NutritionRequirementsRepositoryInterfaces $nutritionRequirementsRepository)
    {
        $this->nutritionRequirementsRepository = $nutritionRequirementsRepository;
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
    public function index()
    {
        try {
            $nutritionRequirements = $this->nutritionRequirementsRepository->getAll();
            return ResponseHelper::jsonResponse(true, 'Data Nutrition Target Berhasil Diambil', NutritionRequirementResource::collection($nutritionRequirements), 200);
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
            $nutritionRequirement = $this->nutritionRequirementsRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Data Nutrition Target Berhasil Ditambahkan', NutritionRequirementResource::make($nutritionRequirement), 200);
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
            $nutritionRequirement = $this->nutritionRequirementsRepository->getById($id);
            if (!$nutritionRequirement) {
                return ResponseHelper::jsonResponse(false, 'Data Nutrition Target Tidak Ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Nutrition Target Berhasil Diambil', NutritionRequirementResource::make($nutritionRequirement), 200);
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
            $nutritionRequirement = $this->nutritionRequirementsRepository->update($id, $request);
            if (!$nutritionRequirement) {
                return ResponseHelper::jsonResponse(false, 'Data Nutrition Target Tidak Ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Nutrition Target Berhasil Diubah', NutritionRequirementResource::make($nutritionRequirement), 200);
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
            $nutritionRequirement = $this->nutritionRequirementsRepository->delete($id);
            if (!$nutritionRequirement) {
                return ResponseHelper::jsonResponse(false, 'Data Nutrition Target Tidak Ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data Nutrition Target Berhasil Dihapus', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
