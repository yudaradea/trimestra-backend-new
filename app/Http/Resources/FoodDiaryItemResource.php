<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodDiaryItemResource extends JsonResource
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
            'quantity' => $this->quantity,
            'food' => $this->whenLoaded('food', function () {
                return new FoodResource($this->food);
            }),

            'user_food' => $this->whenLoaded('userFood', function () {
                return new UserFoodResource($this->userFood);
            }),
        ];
    }
}
