<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'birth_date' => optional($this->birth_date)->format('Y-m-d'),
            'height' => $this->height,
            'weight' => $this->weight,
            'food_allergies' => $this->food_allergies,
            'foto_profile' => $this->foto_profile ? asset('storage/' . $this->foto_profile) : null,
            'no_hp' => $this->no_hp,
            'sleep_duration' => $this->sleep_duration,
            'calculated_bmi' => [
                'bmi' => $this->bmi,
                'bmi_category' => $this->bmi_category,
                'target_weight' => $this->target_weight
            ],
            'is_pregnant' => $this->is_pregnant,
            'weeks' => $this->weeks,
            'trimester' => $this->trimester,
            'hpht' => optional($this->hpht)->format('Y-m-d'),
            'location' => [
                'province_id' => $this->province_id,
                'province_name' => $this->province->name,
                'regency_id' => $this->regency_id,
                'regency_name' => $this->regency->name,
                'district_id' => $this->district_id,
                'district_name' => $this->district->name,
                'village_id' => $this->village_id,
                'village_name' => $this->village->name
            ],
        ];
    }
}
