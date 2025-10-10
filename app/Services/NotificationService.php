<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

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

    public function checkDailyAchievement(User $user, array $percentage): void
    {
        $today = now()->toDateString();

        $isFull = collect($percentage)->every(fn($p) => $p == 100);
        $isPartial = collect($percentage)->every(fn($p) => $p == 75);

        if ($isFull) {
            $this->notifyOnce($user, [
                'type' => 'target_100',
                'title' => 'Target Nutrisi Tercapai 🎉',
                'message' => 'Kamu sudah memenuhi 100% semua target nutrisi harianmu. Hebat!',
                'icon' => 'ri-trophy-line',
            ]);
        } elseif ($isPartial) {
            $this->notifyOnce($user, [
                'type' => 'target_75',
                'title' => 'Target Nutrisi Hampir Tercapai 💪',
                'message' => 'Kamu sudah mencapai 75% semua target nutrisi. Lanjutkan!',
                'icon' => 'ri-run-line',
            ]);
        }
    }

    private function notifyOnce(User $user, array $data): void
    {
        $exists = $user->notifications()
            ->where('type', $data['type'])
            ->whereDate('date', now()->toDateString())
            ->exists();

        if (!$exists) {
            app(NotificationService::class)->create(
                $user->id,
                $data['title'],
                $data['message'],
                $data['icon'],
                $data['type']
            );
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
