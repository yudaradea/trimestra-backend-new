<?php

namespace App\Http\Resources;

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
            'date' => $this->date,
            'duration' => $this->duration,
            'calories_burned' => $this->calories_burned,
            'exercise' => $this->whenLoaded('exercise', function () {
                return new ExerciseResource($this->exercise);
            }),
            'user_exercise' => $this->whenLoaded('userExercise', function () {
                return new UserExerciseResource($this->userExercise);
            }),
        ];
    }
}
