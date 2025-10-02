<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordRequest extends FormRequest
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
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    // public function withValidator($validator)
    // {

    //     $validator->after(function ($validator) {
    //         $user = Auth::user();
    //         if ($this->current_password && !Hash::check($this->current_password, $user->password)) {
    //             $validator->errors()->add('current_password', 'Password lama salah.');
    //         }
    //     });
    // }

    public function attributes()
    {
        return [
            'current_password' => 'password lama',
            'password' => 'password baru',
            'password_confirmation' => 'konfirmasi password baru',
        ];
    }
}
