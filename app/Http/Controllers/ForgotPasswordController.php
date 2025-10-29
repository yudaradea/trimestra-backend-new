<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\SendPinMail;
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
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak terdaftar.'], 404);
        }

        // --- Pemeriksaan Cooldown ---
        $lastPin = PasswordReset::where('email', $request->email)
            ->latest()
            ->first();

        if ($lastPin) {
            // Konversi created_at menjadi Carbon instance jika belum
            $lastPinCreatedAt = Carbon::parse($lastPin->created_at);

            if ($lastPinCreatedAt->greaterThan(now()->subMinute())) {
                $cooldown = 60 - $lastPinCreatedAt->diffInSeconds(now());
                return response()->json(['message' => "Tunggu {$cooldown} detik sebelum mengirim ulang PIN"], 429);
            }
        }


        // --- Invaliasi PIN Lama ---
        PasswordReset::where('email', $request->email)->delete();


        // Buat PIN baru
        $pin = rand(100000, 999999);

        PasswordReset::insert([
            'email' => $request->email,
            'pin' => $pin,
            'expires_at' => Carbon::now()->addMinutes(10), // PIN berlaku 10 menit
            'created_at' => Carbon::now() // Tambahkan created_at
        ]);

        // Kirim email PIN menggunakan Mailable dan Blade template
        try {
            Mail::to($request->email)->send(new SendPinMail($pin));
        } catch (\Exception $e) {
            // Tangani jika email gagal terkirim
            // \Log::error('Email send failed: ' . $e->getMessage()); // Opsional: logging
            return response()->json(['message' => 'Gagal mengirim email. Silakan coba lagi.'], 500);
        }
        // ---------------------------------------------

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
