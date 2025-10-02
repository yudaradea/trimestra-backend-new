<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ];
    }

    public function attributes()
    {
        return [
            'current_password' => 'password lama',
            'new_password' => 'password baru',
            'new_password_confirmation' => 'konfirmasi password baru',
        ];
    }
}
