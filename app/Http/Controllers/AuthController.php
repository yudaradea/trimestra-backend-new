<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\Notification;
use App\Services\NutritionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;
    private NutritionService $nutritionService;

    public function __construct(AuthRepositoryInterface $authRepository, NutritionService $nutritionService)
    {
        $this->authRepository = $authRepository;
        $this->nutritionService = $nutritionService;
    }

    public function register(RegisterRequest $request)
    {
        $request = $request->validated();

        try {
            $user = $this->authRepository->register($request);

            Notification::create([
                'user_id' => $user->id,
                'title' => "Selamat Datang, $user->name 👋",
                'message' => 'Terima kasih telah bergabung! Yuk mulai isi diary harianmu.',
                'icon' => 'ri-hand-heart-line',
                'type' => 'welcome',
                'date' => now()->toDateString(),
                'time' => now()->format('H:i:s'),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => UserResource::make($user),
                'token' => $token,
                'message' => 'Register Berhasil'
            ]);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        try {
            $user = $this->authRepository->login($credentials);
            // jika login gagal beri pesan
            if (!$user) {
                return ResponseHelper::jsonResponse(false, 'Email atau Password Salah', null, 401);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user' => UserResource::make($user),
                'token' => $token,
                'message' => 'Login Berhasil'
            ]);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ResponseHelper::jsonResponse(true, 'Logout Berhasil', null, 200);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return ResponseHelper::jsonResponse(false, 'Password Lama Salah', null, 401);
        }

        // jika password baru sama dengan password lama
        if (Hash::check($request->new_password, $user->password)) {
            return ResponseHelper::jsonResponse(false, 'Password Baru Tidak Boleh Sama Dengan Password Lama', null, 401);
        }

        // Update password baru
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return ResponseHelper::jsonResponse(true, 'Password Berhasil Diubah', null, 200);
    }
}
