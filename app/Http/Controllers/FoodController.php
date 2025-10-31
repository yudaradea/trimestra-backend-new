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

        // 1. Cek Profil Ada
        if (!$profile) {
            $foods = Food::inRandomOrder()->limit(8)->get();
            return ResponseHelper::jsonResponse(true, 'Data makanan berhasil diambil', FoodResource::collection($foods), 200);
        }

        $allergies = $profile->food_allergies ?? []; // Pastikan ini selalu array, meskipun null

        // Asumsi: Nilai yang TIDAK perlu difilter adalah "Tidak Ada" atau ID 1
        $no_allergy_values = ["Tidak Ada", 1];

        // Membersihkan array alergi dari nilai-nilai yang tidak relevan (seperti "Tidak Ada")
        // Alergi yang tersisa di $validAllergies adalah yang harus dikecualikan
        $validAllergies = collect($allergies)
            ->reject(fn($value) => in_array($value, $no_allergy_values))
            ->values()
            ->toArray();

        // 2. Jika array alergi yang valid (yang harus dikecualikan) KOSONG
        if (empty($validAllergies)) {
            // User tidak punya alergi atau hanya memilih "Tidak Ada"
            $foods = Food::inRandomOrder()->limit(8)->get();
            return ResponseHelper::jsonResponse(true, 'Data makanan berhasil diambil', FoodResource::collection($foods), 200);
        }

        // 3. Jika Ada Alergi Valid (filter dan ambil 8 makanan)

        // Ubah array PHP alergi valid menjadi JSON string untuk query database
        $allergiesJson = json_encode($validAllergies);

        $foods = Food::where(function ($query) use ($allergiesJson) {
            $query->whereRaw("NOT JSON_OVERLAPS(allergies, ?)", [$allergiesJson])
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
