<?php

namespace App\Http\Middleware;

use App\Models\Profile;
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
                        $this->notificationService->create(
                            $request->user()->id,
                            'Selamat! Kamu memasuki trimester ' . $newTrimester,
                            'Jaga kesehatan dan perhatikan kebutuhan nutrisi di fase ini 💚',
                            'ri-heart-line'
                        );
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
