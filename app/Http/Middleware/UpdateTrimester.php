<?php

namespace App\Http\Middleware;

use App\Models\Profile;
use App\Services\NutritionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateTrimester
{

    protected $nutritionService;

    public function __construct(NutritionService $nutritionService)
    {
        $this->nutritionService = $nutritionService;
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
                    $profile->update([
                        'weeks' => $usiaKehamilan,
                        'trimester' => $this->calculateTrimester($usiaKehamilan),
                    ]);

                    // Update nutrition requirement
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
