<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->user()->id,

            // aturan untuk update profile
            'birth_date' => 'sometimes|required|date',
            'height' => 'sometimes|required|numeric|min:0',
            'weight' => 'sometimes|required|numeric|min:0',
            'foto_profile' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'no_hp' => 'sometimes|required|string|max:20',
            'sleep_duration' => 'sometimes|required|string|in:<7,7-9,9-11',
            'food_allergies' => 'nullable|array|exists:allergies,name',
            'is_pregnant' => 'sometimes|required|boolean',
            'weeks' => 'nullable|required_if:is_pregnant,true|integer',
            'hpht' => 'nullable|required_if:is_pregnant,true|date',
            'province_id' => 'sometimes|required|integer|exists:provinces,id',
            'regency_id' => [
                'sometimes',
                'required',
                'integer',
                // Pastikan ID kota ada di tabel regencies
                // dan memiliki ID provinsi yang sama dengan ID provinsi yang dipilih
                Rule::exists('regencies', 'id')
                    ->where(fn($q) => $q->where('province_id', request('province_id'))),
            ],

            'district_id' => [
                'sometimes',
                'required',
                'integer',
                // Pastikan ID kecamatan ada di tabel districts
                // dan memiliki ID kota yang sama dengan ID kota yang dipilih
                Rule::exists('districts', 'id')
                    ->where(fn($q) => $q->where('regency_id', request('regency_id'))),
            ],

            'village_id' => [
                'sometimes',
                'required',
                'integer',
                // Pastikan ID desa ada di tabel villages
                // dan memiliki ID kecamatan yang sama dengan ID kecamatan yang dipilih
                Rule::exists('villages', 'id')
                    ->where(fn($q) => $q->where('district_id', request('district_id'))),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nama',
            'email' => 'email',

            // atribute untuk profile
            'birth_date' => 'tanggal lahir',
            'height' => 'tinggi badan',
            'weight' => 'berat badan',
            'foto_profile' => 'foto profile',
            'no_hp' => 'nomor hp',
            'sleep_duration' => 'durasi tidur',
            'food_allergies' => 'alergi makanan',
            'is_pregnant' => 'kehamilan',
            'trimester' => 'trimester',
            'weeks' => 'minggu',
            'hpht' => 'hpht',
            'province_id' => 'provinsi',
            'regency_id' => 'kota/kabupaten',
            'district_id' => 'kecamatan',
            'village_id' => 'desa',
        ];
    }
}
