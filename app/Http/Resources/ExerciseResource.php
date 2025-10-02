<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
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
            'name' => $this->name,
            'calories_burned_per_minute' => $this->calories_burned_per_minute,
            'jenis' => $this->jenis,
            'video_url' => $this->video_url,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];
    }
}
