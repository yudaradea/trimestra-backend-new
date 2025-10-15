<?php

namespace App\Http\Requests\Food;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'food_category_id' => 'required|exists:food_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'allergies' => 'nullable|array|exists:allergies,name',
            'calories' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'ukuran_satuan' => 'required|numeric|max:255',
            'ukuran_satuan_nama' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean'
        ];
    }

    public function attributes()
    {
        return [
            'food_category_id' => 'kategori makanan',
            'name' => 'nama',
            'description' => 'deskripsi',
            'image' => 'gambar',
            'calories' => 'kalori',
            'protein' => 'protein',
            'fat' => 'lemak',
            'carbohydrates' => 'karbohidrat',
            'ukuran_satuan' => 'ukuran satuan (100)',
            'ukuran_satuan_nama' => 'ukuran satuan nama (gram)',
            'is_active' => 'aktif'
        ];
    }
}
