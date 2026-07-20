<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : ($this->route('id') ?? $this->user()->id);

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$userId],
            'mobile' => ['required', 'string', 'max:20', 'unique:users,mobile,'.$userId],
            'address' => ['nullable', 'string'],
            'joining_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'role' => ['nullable', 'string', 'in:MR,Admin'],
            'status' => ['nullable', 'boolean']
        ];
    }
}
