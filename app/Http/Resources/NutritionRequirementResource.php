<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NutritionRequirementResource extends JsonResource
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
            'bmi_category' => $this->bmi_category,
            'is_pregnant' => $this->is_pregnant,
            'trimester' => $this->trimester,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbohydrates' => $this->carbohydrates,
            'fat' => $this->fat
        ];
    }
}
