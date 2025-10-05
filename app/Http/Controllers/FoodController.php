<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Food\StoreRequest;
use App\Http\Requests\Food\UpdateRequest;
use App\Http\Resources\FoodResource;
use App\Http\Resources\PaginateResource;
use App\Interfaces\FoodRepositoryInterface;
use App\Models\Food;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FoodController extends Controller implements HasMiddleware
{
    private FoodRepositoryInterface $foodRepository;

    public function __construct(FoodRepositoryInterface $foodRepository)
    {
        $this->foodRepository = $foodRepository;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('admin', except: ['index', 'show', 'getAllPaginated', 'getRecomendedFoods']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $foods = $this->foodRepository->getAll(
                $request->search,
                $request->limit,
                // filter sesuai kategori
                $request->filterByFoodCategoryId,
                true
            );

            return ResponseHelper::jsonResponse(true, 'Data makanan berhasil diambil', FoodResource::collection($foods), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string',
            'filterByFoodCategoryId' => 'nullable|string',
            'row_per_page' => 'required|integer',
        ]);

        try {
            $foods = $this->foodRepository->getAllPaginated(
                $request->search,
                $request->row_per_page,
                $request->filterByFoodCategoryId
            );

            return ResponseHelper::jsonResponse(true, 'Data makanan berhasil diambil', PaginateResource::make($foods, FoodResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function getRecomendedFoods(Request $request)
    {
        $profile = $request->user()->profile;

        // Jika user belum punya profile atau tidak punya alergi → random food
        if (!$profile || empty($profile->food_allergies) || $profile->food_allergies == 'tidak punya') {
            $foods = Food::inRandomOrder()->limit(8)->get();
            return ResponseHelper::jsonResponse(true, 'Data makanan berhasil diambil', FoodResource::collection($foods), 200);
        }

        // Jika punya alergi → exclude makanan dengan alergi tsb
        $allergies = json_encode($profile->food_allergies);

        $foods = Food::where(function ($query) use ($allergies) {
            $query->whereRaw("NOT JSON_OVERLAPS(allergies, ?)", [$allergies])
                ->orWhereNull('allergies');
        })
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return ResponseHelper::jsonResponse(true, 'Data makanan berhasil diambil', FoodResource::collection($foods), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $request = $request->validated();

        try {
            $food = $this->foodRepository->create($request);

            return ResponseHelper::jsonResponse(true, 'Makanan Berhasil Dibuat', FoodResource::make($food), 200);
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
            $food = $this->foodRepository->getById($id);

            if (!$food) {
                return ResponseHelper::jsonResponse(false, 'Data makanan tidak ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data makanan berhasil diambil', FoodResource::make($food), 200);
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
            $food = $this->foodRepository->getById($id);

            if (!$food) {
                return ResponseHelper::jsonResponse(false, 'Data makanan tidak ditemukan', null, 404);
            }

            $food = $this->foodRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Makanan Berhasil Diupdate', FoodResource::make($food), 200);
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
            $food = $this->foodRepository->getById($id);

            if (!$food) {
                return ResponseHelper::jsonResponse(false, 'Data makanan tidak ditemukan', null, 404);
            }

            $this->foodRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Makanan Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
