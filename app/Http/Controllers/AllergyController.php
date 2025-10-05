<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Allergy\StoreUpdateRequest;
use App\Http\Resources\AllergyResource;
use App\Interfaces\AllergyRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AllergyController extends Controller implements HasMiddleware
{
    private AllergyRepositoryInterface $allergyRepository;

    public function __construct(AllergyRepositoryInterface $allergyRepository)
    {
        $this->allergyRepository = $allergyRepository;
    }

    public static function middleware(): array
    {
        return
            [
                new Middleware('admin', except: ['index', 'show']),
            ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $allergies = $this->allergyRepository->getAll();
            return ResponseHelper::jsonResponse(true, 'Data alergi berhasil diambil', AllergyResource::collection($allergies), 200);
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
            $allergy = $this->allergyRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Alergi Berhasil Dibuat', AllergyResource::make($allergy), 200);
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
            $allergy = $this->allergyRepository->getById($id);
            if (!$allergy) {
                return ResponseHelper::jsonResponse(false, 'Data alergi tidak ditemukan', null, 404);
            }
            return ResponseHelper::jsonResponse(true, 'Data alergi berhasil diambil', AllergyResource::make($allergy), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUpdateRequest $request, string $id)
    {
        $request = $request->validated();

        try {
            $allergy = $this->allergyRepository->getById($id);
            if (!$allergy) {
                return ResponseHelper::jsonResponse(false, 'Data alergi tidak ditemukan', null, 404);
            }
            $allergy = $this->allergyRepository->update($id, $request);
            return ResponseHelper::jsonResponse(true, 'Alergi Berhasil Diupdate', AllergyResource::make($allergy), 200);
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
            $allergy = $this->allergyRepository->getById($id);
            if (!$allergy) {
                return ResponseHelper::jsonResponse(false, 'Data alergi tidak ditemukan', null, 404);
            }
            $this->allergyRepository->delete($id);
            return ResponseHelper::jsonResponse(true, 'Alergi Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
