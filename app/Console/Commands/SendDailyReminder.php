<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\NotificationService;

class SendDailyReminder extends Command
{
    protected $signature = 'reminder:daily';
    protected $description = 'Kirim notifikasi harian ke semua user';

    public function handle()
    {
        $users = User::all();
        $service = app(NotificationService::class);

        foreach ($users as $user) {
            $service->create(
                $user->id,
                'Jangan lupa penuhi target harianmu!',
                'Cek asupan makanan dan aktivitasmu hari ini 💪',
                'ri-lightbulb-line'
            );
        }

        $this->info('Notifikasi harian dikirim ke semua user.');
    }
}
