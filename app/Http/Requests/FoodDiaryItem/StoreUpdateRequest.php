<?php

namespace App\Http\Requests\FoodDiaryItem;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'food_diary_id' => 'required|exists:food_diaries,id',
            'food_id' => 'nullable|uuid|exists:food,id',
            'user_food_id' => 'nullable|uuid|exists:user_food,id',
            'quantity' => 'required|numeric|min:0.01',
        ];
    }
}
