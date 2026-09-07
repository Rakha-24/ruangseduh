<?php

namespace App\Http\Requests\Admin;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                Reservation::STATUS_PENDING,
                Reservation::STATUS_CONFIRMED,
                Reservation::STATUS_CANCELLED,
                Reservation::STATUS_COMPLETED,
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status reservasi wajib diisi.',
            'status.in' => 'Status reservasi tidak valid.',
        ];
    }
}