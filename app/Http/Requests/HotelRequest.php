<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelRequest extends FormRequest {
    public function rules(): array {
        return [
            'id'          => ['nullable', 'exists:hotels,id'],
            'name'        => ['required'],
            'city_id'     => ['required', 'exists:cities,id'],
            'address'     => ['required'],
            'stars'       => ['required', 'integer', 'min:1', 'max:5'],
            'image'       => ['nullable', 'url'],
            'description' => ['nullable'],
        ];
    }

    public function authorize(): bool {
        return true;
    }
}
