<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // buat date dengan jam juga
            'date' => $this->date,
            'duration' => $this->duration,
            'calories_burned' => $this->calories_burned,
            'from_device' => $this->from_device,
            'activity_name' => $this->activity_name,
            'exercise' => $this->whenLoaded('exercise', function () {
                return new ExerciseResource($this->exercise);
            }),
            'user_exercise' => $this->whenLoaded('userExercise', function () {
                return new UserExerciseResource($this->userExercise);
            }),
            'created_at' => $this->created_at,
        ];
    }
}
