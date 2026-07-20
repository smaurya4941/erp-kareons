<?php

namespace App\Http\Requests\DoctorVisit;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductDiscussionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('MR');
    }

    public function rules(): array
    {
        return [
            'doctor_visit_id' => ['required', 'integer', 'exists:doctor_visits,id'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.interest_level' => ['required', 'in:Very High,High,Medium,Low,Not Interested'],
            'products.*.remarks' => ['nullable', 'string', 'max:500'],
        ];
    }
}
