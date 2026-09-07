<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DiningTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama meja wajib diisi.',
            'name.string' => 'Nama meja harus berupa teks.',
            'name.max' => 'Nama meja maksimal 255 karakter.',
            'capacity.required' => 'Kapasitas kursi wajib diisi.',
            'capacity.integer' => 'Kapasitas kursi harus berupa angka.',
            'capacity.min' => 'Kapasitas kursi minimal 1 kursi.',
            'capacity.max' => 'Kapasitas kursi maksimal 500 kursi.',
            'is_active.boolean' => 'Status aktif harus berupa nilai benar/salah.',
        ];
    }
}