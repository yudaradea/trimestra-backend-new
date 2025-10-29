<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Mengirim PIN reset password dan menginvalidasi PIN lama.
     */
    public function sendPin(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Berikan pesan umum untuk keamanan (agar tidak membocorkan keberadaan email)
            return response()->json(['message' => 'Jika email terdaftar, PIN akan dikirimkan.'], 200);
        }

        // --- PERBAIKAN KRUSIAL: Pemeriksaan Cooldown ---
        // Cek apakah PIN baru-baru ini dikirim (dalam 60 detik terakhir)
        $lastPin = PasswordReset::where('email', $request->email)
            ->latest() // Ambil yang terbaru
            ->first();

        if ($lastPin && $lastPin->created_at->greaterThan(now()->subMinute())) {
            // Jika ada PIN yang dibuat dalam 1 menit terakhir, tolak.
            $cooldown = 60 - $lastPin->created_at->diffInSeconds(now());
            return response()->json(['message' => "Tunggu {$cooldown} detik sebelum mengirim ulang PIN"], 429);
        }

        // --- PERBAIKAN KRUSIAL: Invaliasi PIN Lama ---
        // Hapus SEMUA PIN yang belum digunakan/kedaluwarsa untuk email ini
        PasswordReset::where('email', $request->email)->delete();

        // Buat PIN baru
        $pin = rand(100000, 999999);

        PasswordReset::create([
            'email' => $request->email,
            'pin' => $pin,
            'expires_at' => Carbon::now()->addMinutes(10), // PIN berlaku 10 menit
        ]);

        // Kirim email PIN
        Mail::raw("PIN reset password Anda adalah: {$pin}. PIN ini berlaku 10 menit.", function ($message) use ($request) {
            $message->to($request->email)->subject('Reset Password PIN');
        });

        return response()->json(['message' => 'PIN baru telah dikirim ke email Anda.']);
    }

    /**
     * Memverifikasi PIN.
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pin' => 'required|digits:6',
        ]);

        // Ambil catatan PIN, pastikan belum kedaluwarsa.
        $record = PasswordReset::where('email', $request->email)
            ->where('pin', $request->pin)
            ->where('expires_at', '>', Carbon::now()) // Hanya PIN yang belum kadaluarsa
            ->latest() // Ambil PIN terbaru (yang paling baru dibuat)
            ->first();

        if (!$record) {
            // PIN salah, atau kedaluwarsa, atau sudah dihapus (karena ada PIN baru)
            return response()->json(['message' => 'PIN tidak valid atau kedaluwarsa.'], 400);
        }

        // Catatan: Anda tidak perlu menghapus record di sini. Cukup verifikasi.

        return response()->json(['message' => 'PIN valid']);
    }

    /**
     * Mereset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pin' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        // Cari record PIN yang valid dan belum kedaluwarsa
        $record = PasswordReset::where('email', $request->email)
            ->where('pin', $request->pin)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return response()->json(['message' => 'PIN tidak valid atau kedaluwarsa.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // Ini seharusnya tidak terjadi jika alur diikuti, tapi sebagai safety net
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
        }

        // Reset password
        $user->password = Hash::make($request->password);
        $user->save();

        // --- PERBAIKAN KRUSIAL: Hapus PIN setelah digunakan ---
        $record->delete();

        return response()->json(['message' => 'Password berhasil direset']);
    }
}
