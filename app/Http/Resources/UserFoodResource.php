<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserFoodResource extends JsonResource
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
            'calories' => $this->calories,
            'protein' => $this->protein,
            'fat' => $this->fat,
            'carbohydrates' => $this->carbohydrates,
            'ukuran_satuan' => $this->ukuran_satuan,
            'ukuran_satuan_nama' => $this->ukuran_satuan_nama
        ];
    }
}
