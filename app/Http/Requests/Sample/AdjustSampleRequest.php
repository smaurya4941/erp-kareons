<?php

namespace App\Http\Requests\Sample;

use Illuminate\Foundation\Http\FormRequest;

class AdjustSampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'action_type' => ['required', 'string', 'in:reduce,return,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'], // Must be positive here, we negate it in service
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
