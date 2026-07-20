<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Middleware handles actual auth/role check
    }

    public function rules(): array
    {
        // Handle both web ({product}) and api ({product}) binding
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:products,name,' . $productId],
            // Only the name is required for now; the rest are optional.
            'category' => ['nullable', 'string', 'max:255'],
            'strength' => ['nullable', 'string', 'max:255'],
            'pack_size' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'boolean'],
            
            // Future-ready fields
            'brand' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'hsn_code' => ['nullable', 'string', 'max:50'],
            'mrp' => ['nullable', 'numeric', 'min:0'],
            'gst' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:255'],
            'expiry_required' => ['nullable', 'boolean'],
        ];
    }
}
