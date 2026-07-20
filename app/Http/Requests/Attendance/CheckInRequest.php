<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // The selfie can be a standard file upload, or a Base64 string from a mobile app.
            // For now we'll support both, but primarily standard file for Web.
            'selfie' => ['required', 'image', 'max:5120'], // Max 5MB
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric'], // meters
            'address' => ['nullable', 'string', 'max:1000'],
            'device_info' => ['nullable', 'array'],
        ];
    }
}
