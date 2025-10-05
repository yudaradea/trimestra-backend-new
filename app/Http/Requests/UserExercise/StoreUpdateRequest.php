<?php

namespace App\Http\Requests\UserExercise;

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
            'calories_burned_per_minute' => 'required|numeric|min:0',
            'jenis' => 'nullable|string|max:255',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nama',
            'calories_burned_per_minute' => 'Kalori yang dibakar per menit',
            'jenis' => 'Jenis Exercise contoh (Cardio)',
        ];
    }
}
