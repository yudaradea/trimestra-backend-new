<?php

namespace App\Http\Requests\ExerciseLog;

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
            'exercise_id' => 'nullable|exists:exercises,id',
            'user_exercise_id' => 'nullable|exists:user_exercises,id',
            'duration' => 'required|numeric|min:1',
            'date' => 'required|date',

        ];
    }

    public function attributes()
    {
        return [
            'exercise_id' => 'latihan',
            'user_exercise_id' => 'user_latihan',
            'duration' => 'durasi',
            'date' => 'tanggal',
        ];
    }
}
