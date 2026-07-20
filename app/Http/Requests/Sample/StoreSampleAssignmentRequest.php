<?php

namespace App\Http\Requests\Sample;

use Illuminate\Foundation\Http\FormRequest;

class StoreSampleAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            
            // Allow multiple products to be assigned at once
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'products.required' => 'At least one product must be assigned.',
            'products.*.product_id.required' => 'Please select a valid product.',
            'products.*.quantity.min' => 'Assigned quantity must be at least 1.',
        ];
    }
}
