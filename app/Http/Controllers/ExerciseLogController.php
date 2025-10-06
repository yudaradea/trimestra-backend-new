<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ExerciseLog\StoreUpdateRequest;
use App\Http\Resources\ExerciseLogResource;
use App\Http\Resources\PaginateResource;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use App\Models\UserExercise;
use Exception;
use Illuminate\Http\Request;

class ExerciseLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        try {
            $exerciseLogs = ExerciseLog::where('user_id', $user->id)
                ->when($request->date, function ($query, $date) {
                    $query->where('date', $date);
                })
                ->with(['exercise', 'userExercise'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($request->limit ?? 50)
                ->get();

            return ResponseHelper::jsonResponse(true, 'Data Berhasil Diambil', ExerciseLogResource::collection($exerciseLogs), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 400);
        }
    }

    public function getAllPagination(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'date' => 'nullable|date',
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer'
        ]);
        try {
            $query = ExerciseLog::where('user_id', $user->id)
                ->when($request->date, function ($query, $date) {
                    $query->where('date', $date);
                })
                ->with(['exercise', 'userExercise'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc');

            if ($request->search) {
                $query->search($request->search);
            }

            $exerciseLogs = $query->paginate($request->row_per_page);

            return ResponseHelper::jsonResponse(true, 'Data Berhasil Diambil', PaginateResource::make($exerciseLogs, ExerciseLogResource::class), 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUpdateRequest $request)
    {
        $user = $request->user();

        try {
            $data = $request->validated();

            $caloriesBurndPerMinute = 0;

            $exerciseId = $data['exercise_id'] ?? null;
            $userExerciseId = $data['user_exercise_id'] ?? null;

            if ($exerciseId) {
                $exercise = Exercise::findOrFail($exerciseId);
                $caloriesBurndPerMinute = $exercise->calories_burned_per_minute;
            } elseif ($userExerciseId) {
                $userExercise = UserExercise::findOrFail($userExerciseId);
                $caloriesBurndPerMinute = $userExercise->calories_burned_per_minute;
            }

            $caloriesBurned = $caloriesBurndPerMinute * $data['duration'];

            $exerciseLogs = ExerciseLog::create([
                'user_id'          => $user->id,
                'exercise_id'      => $exerciseId,
                'user_exercise_id' => $userExerciseId,
                'duration'         => $data['duration'],
                'calories_burned'  => $caloriesBurned,
                'date'             => $data['date'],
            ]);

            return ResponseHelper::jsonResponse(
                true,
                'Data Berhasil Disimpan',
                ExerciseLogResource::make($exerciseLogs->load(['exercise', 'userExercise'])),
                200
            );
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 400);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $user = $request->user();

        try {
            $exerciseLogs = ExerciseLog::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['exercise', 'userExercise'])
                ->first();

            if (!$exerciseLogs) {
                return ResponseHelper::jsonResponse(false, 'Data Tidak Ditemukan', null, 404);
            }

            return ResponseHelper::jsonResponse(true, 'Data Berhasil Diambil', ExerciseLogResource::make($exerciseLogs->load(['exercise', 'userExercise'])), 200);
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

        try {
            $data = $request->validated();

            $exerciseLogs = ExerciseLog::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$exerciseLogs) {
                return ResponseHelper::jsonResponse(false, 'Data Tidak Ditemukan', null, 404);
            }

            $exercise = null;
            $caloriesBurndPerMinute = 0;

            if ($data['exercise_id']) {
                $exercise = Exercise::findOrFail($data['exercise_id']);
                $caloriesBurndPerMinute = $exercise->calories_burned_per_minute;
            } elseif ($data['user_exercise_id']) {
                $userExercise = UserExercise::findOrFail($data['user_exercise_id']);
                $caloriesBurndPerMinute = $userExercise->calories_burned_per_minute;
            }

            $caloriesBurned = $caloriesBurndPerMinute * $data['duration'];

            $exerciseLogs->update([
                'exercise_id' => $data['exercise_id'] ?? null,
                'user_exercise_id' => $data['user_exercise_id'] ?? null,
                'duration' => $data['duration'],
                'calories_burned' => $caloriesBurned,
                'date' => $data['date'],
            ]);

            return ResponseHelper::jsonResponse(true, 'Data Berhasil Diupdate', ExerciseLogResource::make($exerciseLogs->load(['exercise', 'userExercise'])), 200);
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
            $exerciseLogs = ExerciseLog::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$exerciseLogs) {
                return ResponseHelper::jsonResponse(false, 'Data Tidak Ditemukan', null, 404);
            }

            $exerciseLogs->delete();

            return ResponseHelper::jsonResponse(true, 'Data Berhasil Dihapus', null, 200);
        } catch (Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }
}
