<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\NutritionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateTrimester
{

    protected $nutritionService;
    protected $notificationService;

    public function __construct(NutritionService $nutritionService, NotificationService $notificationService)
    {
        $this->nutritionService = $nutritionService;
        $this->notificationService = $notificationService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $profile = Profile::where('user_id', $request->user()->id)->first();

            if ($profile && $profile->is_pregnant && $profile->hpht) {
                $usiaKehamilan = $profile->hpht->diffInWeeks(now());

                if ($usiaKehamilan !== $profile->weeks) {
                    $newTrimester = $this->calculateTrimester($usiaKehamilan);

                    if ($newTrimester !== $profile->trimester) {
                        $today = now()->toDateString();
                        $this->notifyOnce($request->user(), [
                            'title' => 'Selamat! Kamu memasuki trimester ke-' . $newTrimester,
                            'message' => 'Jaga kesehatan dan perhatikan kebutuhan nutrisi di fase ini 💚',
                            'icon' => 'ri-heart-line',
                            'type' => 'trimester ke' . $newTrimester,
                            'date' => $today
                        ]);
                    }

                    $profile->update([
                        'weeks' => $usiaKehamilan,
                        'trimester' => $newTrimester,
                    ]);

                    $this->nutritionService->updateProfileAndNutrition($request->user()->id);
                }
            }
        }

        return $next($request);
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
                $data['type'],
                $data['date']
            );
        }
    }

    private function calculateTrimester($weeks)
    {
        if ($weeks >= 1 && $weeks <= 13) {
            return 1;
        } elseif ($weeks >= 14 && $weeks <= 27) {
            return 2;
        } elseif ($weeks >= 28 && $weeks <= 42) {
            return 3;
        }

        return null;
    }
}
