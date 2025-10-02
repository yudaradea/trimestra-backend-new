<?php

namespace App\Http\Requests\UserFood;

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
            'name' => 'required|string|max:255',
            'calories' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'ukuran_satuan' => 'nullable|numeric|min:0.01',
            'ukuran_satuan_nama' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'calories' => 'Kalori',
            'carbohydrates' => 'Karbohidrat',
            'fat' => 'Lemak',
            'protein' => 'Protein',
            'ukuran_satuan' => 'Ukuran Satuan dalam gram contoh (100)',
            'ukuran_satuan_nama' => 'Ukuran Satuan Nama contoh (gram)',
        ];
    }
}
