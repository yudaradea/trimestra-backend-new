<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function create($userId, $title, $message = null, $icon = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'date' => now()->toDateString(),
            'time' => now()->format('H:i'),
        ]);
    }

    public function markAsRead($notificationId)
    {
        $notif = Notification::find($notificationId);
        if ($notif) {
            $notif->read = true;
            $notif->save();
        }
    }

    public function getUserNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();
    }

    public function checkDailyAchievement($user, $report)
    {
        $summary = (array)($report->summary ?? []);
        $target = (array)($report->target ?? []);

        if (!$summary || !$target) return;

        $nutrients = ['calories', 'protein', 'carbohydrates', 'fat'];
        $achieved = 0;

        foreach ($nutrients as $key) {
            $intake = $summary["{$key}_intake"] ?? 0;
            $goal = $target[$key] ?? 1;
            $percent = ($intake / $goal) * 100;

            if ($percent >= 100) {
                $achieved++;
            } elseif ($percent >= 70 && $percent < 80) {
                $this->notifyOnce($user->id, "Kamu hampir mencapai target {$key} hari ini!", "Tinggal sedikit lagi 💪", 'ri-run-line');
            }
        }

        if ($achieved === count($nutrients)) {
            $this->notifyOnce($user->id, "Selamat! Kamu capai semua target hari ini 🎉", "Pertahankan konsistensimu 💚", 'ri-trophy-line');
        }
    }

    private function notifyOnce($userId, $title, $message, $icon)
    {
        $exists = Notification::where('user_id', $userId)
            ->where('title', $title)
            ->whereDate('date', now()->toDateString())
            ->exists();

        if (!$exists) {
            app(NotificationService::class)->create($userId, $title, $message, $icon);
        }
    }

    public function getUnreadNotifications($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();
    }

    public function delete($id)
    {
        Notification::where('id', $id)->delete();
    }
}
