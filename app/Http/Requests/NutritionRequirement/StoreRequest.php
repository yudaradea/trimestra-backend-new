<?php

namespace App\Http\Requests\NutritionRequirement;

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
            'bmi_category' => 'required|string|in:underweight,normal,overweight,obese',
            'is_pregnant' => 'required|boolean',
            'trimester' => 'nullable|integer|between:1,3',
            'calories' => 'required|integer',
            'protein' => 'required|numeric',
            'carbohydrates' => 'required|numeric',
            'fat' => 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'bmi_category' => 'kategori bmi',
            'is_pregnant' => 'kehamilan',
            'trimester' => 'trimester',
            'calories' => 'kalori',
            'protein' => 'protein',
            'carbohydrates' => 'karbohidrat',
            'fat' => 'lemak'
        ];
    }
}
