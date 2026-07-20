<?php

namespace App\Http\Requests\DoctorVisit;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Assume MR has permission (middleware handles this)
    }

    public function rules(): array
    {
        return [
            // Location Details
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric'],
            'address' => ['nullable', 'string', 'max:1000'],
            
            // Doctor Details
            'doctor_name' => ['required', 'string', 'max:255'],
            'clinic_name' => ['nullable', 'string', 'max:255'],
            'specialization' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'area' => ['nullable', 'string', 'max:255'],
            'doctor_address' => ['nullable', 'string', 'max:1000'],
            
            // Discussion
            'discussion_summary' => ['required', 'string'],
            'doctor_response' => ['required', 'string', 'max:255'],
            'competitor_medicines' => ['nullable', 'string', 'max:1000'],
            'remarks' => ['nullable', 'string'],
            
            // Discussed Products
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.interest_level' => ['required', 'in:Very High,High,Medium,Low,Not Interested'],
            'products.*.remarks' => ['nullable', 'string', 'max:500'],
            
            // Samples Distributed (Optional)
            'samples' => ['nullable', 'array'],
            'samples.*.product_id' => ['required', 'exists:products,id'],
            'samples.*.quantity' => ['required', 'integer', 'min:1'],
            
            // Orders Collected (Optional)
            'orders' => ['nullable', 'array'],
            'orders.*.product_id' => ['required', 'exists:products,id'],
            'orders.*.quantity' => ['required', 'integer', 'min:1'],
            'order_remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
