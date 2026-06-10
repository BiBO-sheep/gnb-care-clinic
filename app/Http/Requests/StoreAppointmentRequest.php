<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Sanctum sudah handle auth
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'poli_id'   => 'required|integer|exists:polis,id',
            'dokter_id' => 'nullable|integer|exists:users,id',
            'tanggal'   => 'required|string|max:20',
            'jam'       => 'required|string|in:09:00,10:30,11:15,14:00,15:45,17:45',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'poli_id.exists'  => 'Poli yang dipilih tidak valid.',
            'jam.in'          => 'Jam yang dipilih tidak tersedia.',
            'tanggal.max'     => 'Format tanggal terlalu panjang.',
        ];
    }

    /**
     * Override agar return JSON (bukan redirect) untuk API
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
