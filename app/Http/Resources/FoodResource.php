<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
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
            'food_category_id' => $this->food_category_id,
            // nama kategori
            'food_category_name' => $this->foodCategory->name,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image ? asset('storage/' . $this->image) : asset('images/food/default.png'),
            'allergy_ids' => $this->allergies,
            'calories' => $this->calories,
            'protein' => $this->protein,
            'fat' => $this->fat,
            'carbohydrates' => $this->carbohydrates,
            'ukuran_satuan' => $this->ukuran_satuan,
            'ukuran_satuan_nama' => $this->ukuran_satuan_nama
        ];
    }
}
