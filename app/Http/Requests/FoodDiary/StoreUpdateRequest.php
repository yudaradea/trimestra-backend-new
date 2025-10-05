<?php

namespace App\Http\Requests\FoodDiary;

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
            'date' => 'required|date',
            'type' => 'required|in:breakfast,lunch,dinner,snack',
            'items' => 'required|array',
            'items.*.food_id' => 'nullable|uuid|exists:food,id',
            'items.*.user_food_id' => 'nullable|uuid|exists:user_food,id',
            'items.*.quantity' => 'nullable|numeric|min:0.01',
        ];
    }

    public function attributes()
    {
        return [
            'date' => 'tanggal',
            'type' => 'jenis',
            'items.*.food_id' => 'makanan',
            'items.*.user_food_id' => 'makanan',
            'items.*.quantity' => 'jumlah',
        ];
    }
}
