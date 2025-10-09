<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NotificationController extends Controller implements HasMiddleware
{
    protected $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public static function middleware(): array
    {
        return [
            new Middleware('admin', only: ['store']),
        ];
    }

    // Ambil semua notifikasi user
    public function index(Request $request)
    {
        $user = $request->user();
        $limit = $request->query('limit', 10); // default 10
        $page = $request->query('page', 1);

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $total = $user->notifications()->count();

        return response()->json([
            'data' => $notifications,
            'total' => $total,
        ]);
    }

    // Ambil notifikasi yang belum dibaca
    public function unread(Request $request)
    {
        $user = $request->user();
        $notifications = $this->service->getUnreadNotifications($user->id);

        return response()->json($notifications);
    }

    // Admin kirim notifikasi ke user tertentu
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'title' => 'required|string',
            'message' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $notif = $this->service->create(
            $request->user_id,
            $request->title,
            $request->message,
            $request->icon
        );

        return response()->json($notif, 201);
    }

    // Tandai notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $this->service->markAsRead($id);
        return response()->json(['status' => 'read']);
    }

    // Hapus notifikasi
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['status' => 'deleted']);
    }

    // Cek pencapaian harian dan kirim notifikasi
    public function checkAchievement(Request $request)
    {
        $user = $request->user();
        $report = $user->diary()->whereDate('date', now()->toDateString())->first();

        if (!$report) {
            return response()->json(['message' => 'Belum ada data hari ini'], 404);
        }

        $this->service->checkDailyAchievement($user, $report);

        return response()->json(['status' => 'checked']);
    }
}
