<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'about_text' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'instagram' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'opening_hours' => ['nullable', 'array'],
            'opening_hours.*.open' => ['nullable', 'string', 'max:10'],
            'opening_hours.*.close' => ['nullable', 'string', 'max:10'],
            'map_embed_url' => ['nullable', 'url'],
        ];
    }

    /**
     * Customize the validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'address.max' => 'Alamat maksimal 500 karakter.',
            'phone.max' => 'Nomor telepon maksimal 50 karakter.',
            'whatsapp.max' => 'WhatsApp maksimal 50 karakter.',
            'instagram.max' => 'Instagram maksimal 100 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'map_embed_url.url' => 'URL peta tidak valid.',
        ];
    }
}