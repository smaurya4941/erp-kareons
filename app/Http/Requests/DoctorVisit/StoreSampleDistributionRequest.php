<?php

namespace App\Http\Requests\DoctorVisit;

use Illuminate\Foundation\Http\FormRequest;

class StoreSampleDistributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('MR');
    }

    public function rules(): array
    {
        return [
            'doctor_visit_id' => ['required', 'integer', 'exists:doctor_visits,id'],
            'samples' => ['required', 'array', 'min:1'],
            'samples.*.product_id' => ['required', 'exists:products,id'],
            'samples.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
