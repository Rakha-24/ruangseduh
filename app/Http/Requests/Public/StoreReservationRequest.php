<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dining_table_id' => ['required', 'integer', 'exists:dining_tables,id'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'reservation_time' => ['required', 'date_format:H:i'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:50'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'dining_table_id.required' => 'Meja wajib dipilih.',
            'dining_table_id.exists' => 'Meja yang dipilih tidak valid.',
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'customer_phone.required' => 'Nomor HP wajib diisi.',
            'reservation_date.required' => 'Tanggal reservasi wajib diisi.',
            'reservation_date.after_or_equal' => 'Tanggal reservasi tidak boleh sebelum hari ini.',
            'reservation_time.required' => 'Waktu reservasi wajib diisi.',
            'reservation_time.date_format' => 'Format waktu tidak valid.',
            'guest_count.required' => 'Jumlah orang wajib diisi.',
            'guest_count.min' => 'Minimal 1 orang.',
            'guest_count.max' => 'Maksimal 50 orang.',
        ];
    }
}