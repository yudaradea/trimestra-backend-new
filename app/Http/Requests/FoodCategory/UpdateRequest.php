<?php

namespace App\Http\Requests\FoodCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('food_categories', 'name')->ignore($this->route('food_category'))
            ],
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama Kategori Makanan',
            'icon' => 'Icon Kategori Makanan',
        ];
    }
}
