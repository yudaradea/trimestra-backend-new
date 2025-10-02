<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NutritionTargetResource extends JsonResource
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
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbohydrates' => $this->carbohydrates,
            'fat' => $this->fat,
        ];
    }
}
